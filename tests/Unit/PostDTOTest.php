<?php

declare(strict_types=1);

namespace Iadicola\Domain\Tests\Unit;

use Iadicola\Domain\Exception\DTOException;
use PHPUnit\Framework\TestCase;

final class PostDTOTest extends TestCase
{
    public function test_from_array_creates_dto(): void
    {
        $dto = PostDTO::fromArray([
            'id' => 1,
            'name' => 'Post title',
            'description' => 'Post description',
        ]);

        $this->assertSame(1, $dto->id());
        $this->assertSame('Post title', $dto->get('name'));
        $this->assertSame('Post description', $dto->get('description'));
    }

    public function test_to_array_returns_all_properties(): void
    {
        $dto = new PostDTO(
            id: 1,
            name: 'Post title',
            description: 'Post description'
        );

        $this->assertEquals(
            [
                'id' => 1,
                'name' => 'Post title',
                'description' => 'Post description',
            ],
            $dto->toArray()
        );
    }

    public function test_unique_returns_expected_array(): void
    {
        $dto = new PostDTO(
            id: 1,
            name: 'Post title',
            description: 'Post description'
        );

        $this->assertSame(
            ['id' => 'Post title'],
            $dto->unique()
        );
    }

    public function test_get_throws_exception_for_missing_property(): void
    {
        $dto = new PostDTO(
            id: 1,
            name: 'Post title',
            description: 'Post description'
        );

        $this->expectException(DTOException::class);

        $dto->get('not_exists');
    }

    public function test_id_can_be_mutated(): void
    {
        $dto = new PostDTO(
            id: null,
            name: 'Post title',
            description: 'Post description'
        );

        $dto->setId(99);

        $this->assertSame(99, $dto->id());
    }
}
