<?php


declare(strict_types=1);

namespace Iadicola\Domain\Tests\Unit;

use Iadicola\Domain\DTO\BaseDTO;
use Illuminate\Database\Eloquent\Model;

final class PostDTO extends BaseDTO
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $description,
    ) {
        parent::__construct($id);
    }

    public static function fromArray(array $data): static
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            description: $data['description']
        );
    }

    public static function fromModel(Model $model): self
    {
       return parent::fromModel($model);
    }

    public function unique(): array
    {
        return ['id' => $this->name];
    }



    
}

