<?php

declare(strict_types=1);

namespace Iadicola\Domain\Repository;

use Iadicola\Domain\DTO\BaseDTO;
use Iadicola\Domain\Contract\IDTO;
use Illuminate\Database\Eloquent\Model;
use Iadicola\Domain\Exception\DTOException;
use Iadicola\Domain\Contract\IRepositoryDTO;

/**
 * Base repository implementation for DTO-driven persistence.
 *
 * This repository acts as a persistence adapter between
 * Data Transfer Objects (DTOs) and Eloquent models.
 *
 * It supports both:
 * - stateless usage (DTO passed per method call)
 * - stateful usage (DTO set once via setDomain())
 *
 * Responsibilities:
 * - adapt DTO data to Eloquent models
 * - enforce identifiers and unique constraints
 * - centralize create / update / upsert logic
 *
 * This repository MUST NOT contain business logic.
 */
class BaseDTORepository implements IRepositoryDTO
{
    /**
     * BaseRepositoryDTO constructor.
     *
     * @param  Model  $model  Eloquent model instance used by the repository
     * @param BaseDTO $dto DTO instance used bu the repository
     */
    public function __construct(protected Model $model)
    {
    }
    

    /**
     * Update an existing model using data from a DTO.
     *
     * The target model is resolved using:
     * - the DTO unique key(s)
     * - and the provided identifier column (default: "id")
     *
     *
     * @param string|null $columnId Identifier column name
     * @param BaseDTO|null $dto DTO containing update data
     *
     * @return Model Updated Eloquent model instance
     *
     * @throws DTOException If the DTO identifier is missing
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update( BaseDTO $dto, ?string $columId = 'id'): Model
    {
     
        $data = $dto->FilterArrayForUpdateModel($this->model);
        if (null == $dto->get($columId)) {
            throw new DTOException('Id of DTO ' . $dto::class . ' is null');
        }

        $unique = $dto->unique();
        $unique[$columId] = $dto->get($columId);

        $model = $this->model->where($unique)->firstOrFail();
        $model->fill($data);
        $model->save();

        return $model;
    }

    /**
     * Create a new model instance using data from a DTO.
     *
     * Only attributes allowed by the model fillable
     * configuration will be persisted.
     *
     * @param IDTO|null $dto
     * @return Model Newly created model instance
     */
    public function create(?BaseDTO $dto): Model
    {
        $data = $dto->FilterArrayForUpdateModel($this->model);

        return $this->model->create($data);
    }

    /**
     * Create or update a model using data from a DTO.
     *
     * Resolution strategy:
     * - Otherwise, the DTO unique key(s) are used for an upsert.
     *
     * If no DTO is provided, the repository will use the previusly assigned domain DTO 
     *
     * @param  string|null  $columId  Identifier column name (optional)
     * 
     * @param BaseDTO|null $dto
     * 
     * @return Model Created or updated Eloquent model
     *
     * @throws DTOException If neither identifier nor unique keys are provided
     */
    public function createOrUpdate( BaseDTO $dto, ?string $columId = null): Model
    {
        $data = $dto->FilterArrayForUpdateModel($this->model);

        //  Update esplicito per ID
        if ($columId !== null && $dto->get($columId) !== null) {
            return $this->model->updateOrCreate(
                [$columId => $dto->get($columId)],
                $data
            );
        }
        //  Upsert per chiavi logiche
        $unique = $dto->unique();

        if ($unique !== null) {
            $model = $this->model->updateOrCreate(
                $unique,
                $data
            );

            return $model;
        }

        throw new DTOException(
            sprintf(
                'Cannot upsert %s: missing identifier (%s) and unique key',
                    $dto::class,
                $columId
            )
        );

    }

   
}
