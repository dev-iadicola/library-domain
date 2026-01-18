<?php

declare(strict_types=1);

namespace Iadicola\Domain\DTO;

use Iadicola\Domain\Attribute\Persist;
use Iadicola\Domain\Helpers\Filters;
use Iadicola\Domain\Contract\IDTO;
use Iadicola\Domain\Exception\DTOException;
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
    public function __construct(public ?int $id = null)
    {

    }


    /**
     * Convert the DTO into an array suitable for persistence.
     *
     * This method returns an associative array intended to be passed
     * directly to Eloquent model persistence methods such as
     * {@see \Illuminate\Database\Eloquent\Model::create()} or
     * {@see \Illuminate\Database\Eloquent\Model::update()}.
     *
     * Behavior:
     * - Only properties explicitly annotated with {@see Persist}
     *   are included in the resulting array.
     * - Array keys are taken from the {@see Persist::$column} value.
     * - Properties holding another {@see BaseDTO} instance are
     *   automatically converted to their identifier (foreign key).
     * - Uninitialized properties are ignored.
        * - Properties with a null value are skipped unless explicitly
    *   allowed via {@see Persist::$nullable}.

     *
     * This method intentionally does NOT represent the full DTO state.
     * For debugging, logging, or serialization purposes, use a
     * dedicated state serialization method instead.
     *
     * @return array<string, mixed> An array suitable for model persistence
     */
    public function import(): array
    {
        $data = [];
        $data['_relations'] = [];
        $ref = new ReflectionObject($this);

        foreach ($ref->getProperties() as $prop) {
            if (!$prop->isInitialized($this)) {
                continue;
            }

            $attrs = $prop->getAttributes(Persist::class);
            if ($attrs === []) {
                continue;
            }

            /** @var Persist $persist */
            $persist = $attrs[0]->newInstance();

            $value = $prop->getValue($this);

            if ($value instanceof BaseDTO) {
                // FK for DB
                $data[$persist->column] = $value?->id();
                // DTO completed for debug
                $data['_relations'][$prop->getName()] = $value?->toArray();
                continue;
            }

            if ($value === null && $persist->nullable === false) {
                continue;
            }

            $data[$persist->column] = $value;
        }

        return $data;
    }


    public function toArray(): array
    {
        $data = [];
        $ref = new ReflectionObject($this);

        foreach ($ref->getProperties() as $prop) {
            if (!$prop->isInitialized($this)) {
                continue;
            }
            $value = $prop->getValue($this);
            $name = $prop->getName();

            if ($value instanceof BaseDTO) {
                $value = $value->toArray();
            }

            $data[$name] = $value;
        }
        return $data;
    }

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
        return Filters::arrayIntersectKey($this->import(), $model->getFillable());
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
     * Set a DTO value by property name and value.
     *
     *
     * @param string $key
     * @param string $value
     * @return BaseDTO
     *
     * @throws DTOException If the property does not exist
     */
    public function set(string $key, mixed $value): self
    {
        if (!property_exists($this, $key)) {
            throw new DTOException(
                sprintf('Property [%s] does not exist on DTO %s', $key, static::class)
            );
        }

        $this->$key = $value;

        return $this;
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


}
