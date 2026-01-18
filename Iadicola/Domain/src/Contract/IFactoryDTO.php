<?php 
namespace Iadicola\Domain\Contract;

use Iadicola\Domain\DTO\BaseDTO;
use Illuminate\Database\Eloquent\Model;

interface IFactoryDTO {

    public function fromArray(array $data):IDTO;

    public function fromModel(Model $model):IDTO;
}