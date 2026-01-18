<?php
namespace Tests\Unit;

use Iadicola\Domain\Attribute\Persist;
use Iadicola\Domain\Contract\IFactoryDTO;
use Iadicola\Domain\DTO\BaseDTO;
use Illuminate\Database\Eloquent\Model;
use Iadicola\Domain\Context\BaseContext;
use Iadicola\Domain\Factory\BaseFactory;
use Iadicola\Domain\Contract\IStatefulDTORepository;

class UserDTO extends BaseDTO
{
    public function __construct(

        #[Persist('id')]
        public ?int $id = null,

        #[Persist('name')]
        public string $name,

        #[Persist('email')]
        public string $email,

        #[Persist('password')]
        public string $password,
    ) {
        parent::__construct($id);
    }
}
class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];


}

class FactoryUser extends BaseFactory
{


    public function fromArray(array $data): UserDTO
    {
        return new UserDTO(
            id: $data['id'] ?? null,
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
        );
    }
}

class UserContext extends BaseContext
{

    public function factory(): IFactoryDTO
    {

        return parent::baseFactory(new FactoryUser());
    }

    public function repository(): IStatefulDTORepository
    {
        return parent::baseRepository(new User());
    }



}