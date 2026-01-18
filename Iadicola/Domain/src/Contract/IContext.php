<?php
namespace Iadicola\Domain\Contract;

use Iadicola\Domain\DTO\BaseDTO;
use Illuminate\Database\Eloquent\Model;

interface IContext {

    public function baseFactory(IFactoryDTO $iFactory):IFactoryDTO;


    public function baseRepository(Model $model): IStatefulDTORepository;



}