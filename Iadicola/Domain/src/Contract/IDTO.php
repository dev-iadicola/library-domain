<?php
namespace Iadicola\Domain\Contract;
use Illuminate\Database\Eloquent\Model;

/**
 * Data Transfer Object contract.
 *
 * An IDTO represents an immutable (or controlled-mutation) data carrier
 * responsible for:
 * - transporting data between layers
 * - exposing writable attributes for persistence
 * - defining unique keys and identifiers
 *
 * DTOs are the single source of truth for repositories.
 * Repositories MUST NOT accept raw arrays.
 */
interface IDTO
{
    /**
     * Create a DTO instance from a plain array.
     *
     * This method is typically used when hydrating the DTO
     * from external sources such as APIs, requests, or jobs.
     *
     * @param array<string, mixed> $data
     * @return static
     */
   // public static function fromArray(): self;


    /**
     * Convert the DTO into a array representation.
     *
     * This method is intended for update, create or updareOrCreate
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;

    public function import(): array;

    /**
     * Return an array of attributes allowed to be persisted
     * on the given model.
     *
     * Implementations MUST:
     * - respect model fillable 
     * - exclude read-only or computed properties
     *
     * @param Model $model
     * @return array<string, mixed>
     */
    public function FilterArrayForUpdateModel(Model $model): array;

    /**
     * Get the primary identifier of the DTO, if present.
     *
     * This identifier is typically used for update operations.
     *
     * @return int|null
     */
    public function id(): ?int;

    /**
     * Assign the primary identifier to the DTO.
     *
     * This method MAY be used after persistence,
     * but SHOULD NOT be required for DTO creation.
     *
     * @param int $id
     * @return self
     */
    public function setId(int $id): self;

    /**
     * Return the unique key(s) used to resolve the model
     * when an identifier is not available.
     *
     * Example:
     * [
     *   'id' => '1',
     *   'email' => 'user@domain.com'
     * ]
     *
     * @return array<string, mixed>|null
     */
    public function unique(): ?array;

     /**
     * Retrieve a value from the DTO by key.
     *
     * This method provides controlled access to internal data
     * without exposing the underlying structure.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed;

    public function set(string $key, mixed $value): self;

}