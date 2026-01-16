<?php

declare(strict_types=1);

namespace Iadicola\Domain\Repository;

use Illuminate\Database\Eloquent\Model;
use Iadicola\Domain\Repository\BaseDTORepository;

/**
 * Generic DTO-based repository.
 *
 * This class is a thin concrete wrapper around {@see BaseRepositoryDTO}
 * that allows the repository to be instantiated directly with
 * an Eloquent model instance.
 *
 * It is intended to be used when:
 * - no domain-specific repository logic is required
 * - persistence is fully driven by DTOs
 * - a generic CRUD repository is sufficient
 *
 * Example usage:
 * <code>
 * $repo = new DTORepository(new User());
 * $repo->createOrUpdate($userDto);
 * </code>
 */
class DTORepository extends BaseDTORepository
{
    /**
     * Create a new generic DTO repository.
     *
     * @param  Model  $model  Eloquent model managed by the repository
     */
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }
}
