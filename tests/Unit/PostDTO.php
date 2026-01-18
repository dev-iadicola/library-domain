<?php


declare(strict_types=1);

namespace Iadicola\Domain\Tests\Unit;

use Tests\Unit\UserDTO;
use Iadicola\Domain\DTO\BaseDTO;
use Iadicola\Domain\Contract\IDTO;
use Iadicola\Domain\Attribute\Persist;
use Illuminate\Database\Eloquent\Model;
use Iadicola\Domain\Context\BaseContext;
use Iadicola\Domain\Contract\IFactoryDTO;

final class PostDTO extends BaseDTO
{
    public function __construct(
        #[Persist(column: 'id', nullable: false)]
        public ?int $id,
        #[Persist(column: 'name')]
        public string $name,
        #[Persist(column: 'description')]
        public string $description,
        #[Persist('user_id', false)] // post need an author
        ?UserDTO $user = null,
    ) {
        parent::__construct($id);

    }
    public function unique(): array
    {
        return ['id' => $this->name];
    }

}

class Post extends Model
{
    protected $fillable = ['id', 'name', 'description', 'user_id'];
}

class FactoryPost implements IFactoryDTO
{

    public function fromArray(array $data): IDTO
    {

        return new PostDTO(
            id: $data['id'],
            name: $data['name'],
            description: $data['description']
        );
    }

    public function fromModel(Model $model): IDTO
    {
        return $this->fromArray($model->toArray());
    }
}

class PostContext extends BaseContext
{


}

