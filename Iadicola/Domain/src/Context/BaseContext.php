<?php 
namespace Iadicola\Domain\Context;

use Iadicola\Domain\Contract\IContext;
use Iadicola\Domain\Contract\IDTO;
use Iadicola\Domain\Contract\IFactoryDTO;
use Iadicola\Domain\Contract\IStatefulDTORepository;
use Iadicola\Domain\DTO\BaseDTO;
use Iadicola\Domain\Factory\BaseFactoryDTO;
use Iadicola\Domain\Repository\BaseDTORepository;
use Iadicola\Domain\Repository\StatefulDTORepository;
use Illuminate\Database\Eloquent\Model;

abstract class BaseContext implements IContext {

    protected BaseDTO $basedto;

    public function __construct(      
        protected IDTO $idto,
    ){}
    public function baseFactory(IFactoryDTO $iFactory): IFactoryDTO
    {
       return $this->$iFactory;
    }

    public function baseRepository(Model $model): IStatefulDTORepository
    {
       return new StatefulDTORepository(new BaseDTORepository($model), $this->idto);
    }

}