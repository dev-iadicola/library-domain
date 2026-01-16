<?php

declare(strict_types=1);

namespace Iadicola\Domain\DTO;

use Iadicola\Domain\Repository\DTORepository;
use Iadicola\Domain\Helpers\Filters;
use Iadicola\Domain\Contract\IDTO;
use Iadicola\Domain\Exception\DTOException;
use Iadicola\Domain\Repository\StatefulDTORepository;
use Illuminate\Database\Eloquent\Model;
use ReflectionObject;

/**
 * Base implementation for Data Transfer Objects.
 *
 * This abstract class provides shared, domain-agnostic behavior
 * for all DTO implementations, including:
 *
 * - identifier handling
 * - automatic array serialization
 * - model attribute filtering for persistence
 * - safe property access helpers
 *
 * This class DOES NOT:
 * - know how to instantiate concrete DTOs
 * - define domain-specific fields
 * - implement model hydration logic
 *
 * Concrete DTOs MUST:
 * - define their own constructor
 * - implement fromModel() when model hydration is required
 * - implement unique() when upsert behavior is needed
 */
abstract class BaseDTO implements IDTO
{
    /**
     * BaseDTO constructor.
     *
     * The identifier is optional and may be null for
     * DTOs representing.
     *
     * @param int|null $id
     */
    public function __construct(public ?int $id)
    {

    }

    /**
     * Create a DTO instance from a plain array.
     *
     * This method is intentionally NOT implemented at the base level,
     * because the BaseDTO does not know how to construct concrete DTOs.
     *
     * Concrete DTOs MUST implement this method if array hydration
     * is required.
     *
     * @param array<string, mixed> $data
     * @return static
     *
     * @throws DTOException
     */
    public abstract static function fromArray(array $data): static;

    /**
     * Convert the DTO into an associative array representation.
     *
     * The array keys correspond to the DTO property names,
     * and the values to their current state.
     *
     * Only initialized properties are included.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        $reflection = new ReflectionObject($this);
        $props = $reflection->getProperties();
        foreach ($props as $prop) {
            if (!$prop->isInitialized($this)) {
                continue;
            }
            $name = $prop->getName();
            $value = $prop->getValue($this);

            $data[$name] = $value;
        }
        return $data;
    }

    /**
     * Create a DTO instance from an Eloquent model.
     *
     * This method is NOT implemented at the base level because
     * the BaseDTO does not know the concrete DTO constructor
     * nor the required domain fields.
     *
     * Concrete DTOs MUST implement this method when model
     * hydration is needed.
     *
     * @param Model $model
     * @return static
     *
     * @throws DTOException
     */
    public abstract static function fromModel(Model $model): self;

    /**
     * Filter DTO data according to the fillable attributes
     * defined on the given Eloquent model.
     *
     * This method adapts the DTO array representation
     * to a format suitable for persistence.
     *
     * @param Model $model
     * @return array<string, mixed>
     */
    public function FilterArrayForUpdateModel(Model $model): array
    {
        return Filters::arrayIntersectKey($this->toArray(), $model->getFillable());
    }


    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function id(): ?int
    {
        return $this->id;
    }
    /**
     * Retrieve a value from the DTO by property name.
     *
     * This provides controlled access to DTO data without
     * exposing internal state as public properties.
     *
     * @param string $key
     * @return mixed
     *
     * @throws DTOException If the property does not exist
     */
    public function get(string $key): mixed
    {
        if (!property_exists($this, $key)) {
            throw new DTOException(
                sprintf('Property [%s] does not exist on DTO %s', $key, static::class)
            );
        }

        return $this->$key;

    }

    /**
     * Define the unique key(s) used to resolve the model
     * when an identifier is not available.
     *
     * Concrete DTOs MUST override this method when
     * create-or-update behavior is required.
     *
     * @return array<string, mixed>
     *
     * @throws DTOException
     */
    public function unique(): array
    {
        throw new DTOException(
            sprintf('Method unique() must be implemented in DTO %s', static::class)
        );
    }

    /**
     * Create a stateful repository instance bound to this DTO.
     *
     * This method is provided as a convenience helper to reduce
     * repetitive instantiation of repositories in application code.
     *
     * It returns a {@see StatefulDTORepository} that:
     * - is bound to the current DTO instance
     * - wraps a DTORepository configured with the given Eloquent model
     *
     *  Architectural note:
     * This method introduces a controlled dependency from the DTO
     * to the persistence layer. While this would be avoided in a
     * strict domain-driven design, it is considered acceptable here
     * due to the following constraints:
     *
     * - this library is intended for Laravel applications only
     * - DTOs are already tightly coupled to Eloquent models
     * - the goal is to favor pragmatism and developer ergonomics
     *
     * This method SHOULD be used sparingly and primarily in
     * application-level orchestration code.
     *
     * @param Model $model Eloquent model associated with the DTO entity
     * @return StatefulDTORepository
     */
    public function repo(Model $model): StatefulDTORepository
    {
        return new StatefulDTORepository(
            repository: new DTORepository(model: $model),
            dto: $this
        );
    }
}
