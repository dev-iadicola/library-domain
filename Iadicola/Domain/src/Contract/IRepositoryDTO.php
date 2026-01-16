<?php
namespace Iadicola\Domain\Contract;

use Iadicola\Domain\DTO\BaseDTO;
use InvalidArgumentException;
use Iadicola\Domain\Contract\IDTO;
use Illuminate\Database\Eloquent\Model;

/**
 * Repository contract for DTO-driven persistence.
 *
 * This interface defines a standard persistence API where
 * Data Transfer Objects (DTOs) are the single source of truth
 * for create and update operations.
 *
 * Implementations MUST:
 * - never accept raw arrays
 * - rely exclusively on DTOs for data mapping
 * - delegate validation and transformation to the DTO layer
 */
interface IRepositoryDTO
{

    /**
     * Persist a new model instance using data provided by a DTO.
     *
     * The DTO is responsible for:
     * - exposing writable attributes
     * - filtering invalid or non-fillable fields
     *
     * @param  IDTO  $dto  Data Transfer Object containing creation data
     * @return Model Newly created Eloquent model instance
     */
    public function create(BaseDTO $dto): Model;

    /**
     * Update an existing model using data provided by a DTO.
     *
     * The target model is resolved using:
     * - the identifier column (default: "id")
     * - the identifier value exposed by the DTO
     *
     * @param  IDTO   $dto        Data Transfer Object containing update data
     * @param  string|null $columnId Identifier column name (default: "id")
     * @return Model Updated Eloquent model instance
     *
     * @throws InvalidArgumentException If the DTO does not expose the identifier
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no model is found
     */
    public function update(BaseDTO $dto, ?string $columId = 'id'): Model;

    /**
     * Create or update a model using data provided by a DTO.
     *
     * Resolution strategy:
     * - If the identifier column is present in the DTO, an updateOrCreate
     *   is executed using that identifier.
     * - Otherwise, the DTO unique key(s) are used for an upsert operation.
     *
     * @param  IDTO   $dto        Data Transfer Object containing persistence data
     * @param  string|null $columnId Optional identifier column name
     * @return Model Created or updated Eloquent model instance
     *
     * @throws InvalidArgumentException If neither identifier nor unique keys are provided
     */
    public function createOrUpdate(BaseDTO $dto, ?string $columId = 'id'): Model;
}
