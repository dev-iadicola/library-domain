<?php
namespace Iadicola\Domain\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Iadicola\Domain\Tests\Unit\FakeDTO;
use Illuminate\Database\Eloquent\Model;
use Iadicola\Domain\DTO\BaseDTO;
use Iadicola\Domain\Exception\DTOException;

final class BaseDTOTest extends TestCase
{
    public function test_to_array(): void
    {
        $dto = new FakeDTO(1, 'Luigi');

        $this->assertEquals(
            ['id' => 1, 'name' => 'Luigi'],
            $dto->toArray()
        );

    }

    public function test_get_existing_property(): void
    {
        $dto = new FakeDTO(1, 'Luigi');

        $this->assertSame('Luigi', $dto->get('name'));
    }

    public function test_get_throws_exception(): void
    {
        $this->expectException(DTOException::class);

        (new FakeDTO(1, 'Luigi'))->get('nope');
    }

    public function test_set_and_get_id(): void
    {
        $dto = new FakeDTO(null, 'Luigi');

        $dto->setId(10);

        $this->assertSame(10, $dto->id());
    }
}