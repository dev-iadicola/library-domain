<?php

declare(strict_types=1);

namespace Iadicola\Domain\Tests\Unit;

use Iadicola\Domain\DTO\BaseDTO;
use Illuminate\Database\Eloquent\Model;

final class FakeDTO extends BaseDTO
{
    public function __construct(
        ?int $id,
        public string $name
    ) {
        parent::__construct($id);
    }

    public static function fromArray(array $data): static
    {
        throw new \RuntimeException();
    }

    public static function fromModel(Model $model): self
    {
        throw new \RuntimeException();
    }

    public function unique(): array
    {
        return ['name' => $this->name];
    }
}

