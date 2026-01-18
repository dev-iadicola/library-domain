<?php

declare(strict_types=1);

namespace Iadicola\Domain\Tests\Unit;

use Iadicola\Domain\Attribute\Persist;
use Iadicola\Domain\DTO\BaseDTO;
use Illuminate\Database\Eloquent\Model;

final class FakeDTO extends BaseDTO
{
    #[Persist('id', false)]
    public ?int $id;

    public function __construct(
        ?int $id,
          #[Persist('name')]
        public string $name
    ) {
        parent::__construct($id);
        $this->id = $id;
    }

    
    public function unique(): array
    {
        return ['name' => $this->name];
    }
}

