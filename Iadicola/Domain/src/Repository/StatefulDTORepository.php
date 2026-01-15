<?php 
namespace Iadicola\Domain\Repository;

use Iadicola\Domain\Contract\IDTO;
use Illuminate\Database\Eloquent\Model;
use Iadicola\Domain\Repository\BaseDTORepository;

/**
 * Stateful repository wrapper for DTO-driven persistence.
 *
 * This class represents an entity-specific repository instance
 * bound to a single Data Transfer Object (DTO).
 *
 * Unlike stateless repositories, this class:
 * - is constructed with a fixed DTO
 * - does not allow changing the DTO after instantiation
 * - exposes a simplified persistence API
 *
 * Design rationale:
 * In this architecture, each DTO represents a single domain entity
 * and corresponds to a specific Eloquent model.
 * Binding the DTO in the constructor enforces this relationship
 * and avoids mutable or implicit repository state.
 *
 * Responsibilities:
 * - delegate persistence operations to a stateless repository
 * - provide an explicit, intention-revealing API
 * - reduce boilerplate in application services
 *
 * This class MUST NOT:
 * - contain business logic
 * - perform validation
 * - alter DTO state
 *
 * It is intended to be used in application services
 * and orchestration layers, not in domain logic.
 */
final class StatefulDTORepository
{
    /**
     * Create a new stateful repository bound to a DTO.
     *
     * @param BaseDTORepository $repository Stateless repository handling persistence logic
     * @param IDTO $dto Data Transfer Object representing the entity to persist
     */
    public function __construct(
        private BaseDTORepository $repository,
        private IDTO $dto
    ) {}

    /**
     * Persist a new model instance using the bound DTO.
     *
     * @return Model Newly created Eloquent model instance
     */
    public function create(): Model
    {
        return $this->repository->create($this->dto);
    }

    /**
     * Update an existing model instance using the bound DTO.
     *
     * The target model is resolved using the identifier
     * exposed by the DTO.
     *
     * @param string|null $columnId Identifier column name (default: "id")
     * @return Model Updated Eloquent model instance
     */
    public function update(?string $columnId = 'id'): Model
    {
        return $this->repository->update($this->dto, $columnId);
    }

    /**
     * Create or update a model instance using the bound DTO.
     *
     * Resolution strategy:
     * - If the identifier column is present in the DTO,
     *   updateOrCreate is executed using that identifier.
     * - Otherwise, the DTO unique key(s) are used for upsert.
     *
     * @param string|null $columnId Optional identifier column name
     * @return Model Created or updated Eloquent model instance
     */
    public function createOrUpdate(?string $columnId = null): Model
    {
        return $this->repository->createOrUpdate($this->dto, $columnId);
    }
}
