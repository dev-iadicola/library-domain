<?php

declare(strict_types=1);

namespace Iadicola\Domain\Tests\Unit;

use Iadicola\Domain\Repository\DTORepository;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Model;

final class DTORepositoryTest extends TestCase
{
    public function test_repository_instantiates(): void
    {
        $repo = new DTORepository(new FakeModel());

        $this->assertInstanceOf(DTORepository::class, $repo);
    }
}
