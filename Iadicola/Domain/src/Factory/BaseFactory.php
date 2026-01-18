<?php
namespace Iadicola\Domain\Factory;

use Iadicola\Domain\Contract\IDTO;
use Illuminate\Database\Eloquent\Model;
use Iadicola\Domain\Contract\IFactoryDTO;
use Iadicola\Domain\Src\Exception\FactoryDTOException;

abstract class BaseFactory implements IFactoryDTO
{
    abstract public function fromArray(array $data): IDTO;
    

    public function fromModel(Model $model): IDTO
    {
        return $this->fromArray($model->toArray());
    }
}