<?php


declare(strict_types=1);

namespace Iadicola\Domain\Tests\Unit;

use Iadicola\Domain\Repository\StatefulDTORepository;
use Iadicola\Domain\Repository\BaseDTORepository;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Model;

final class FakeModel extends Model
{
    protected $fillable = ['name'];
}

final class FakeRepository extends BaseDTORepository
{
    public function __construct()
    {
        parent::__construct(new FakeModel());
    }

    public function create($dto): Model
    {
        return new FakeModel(['name' => $dto->name]);
    }
}

final class StatefulDTORepositoryTest extends TestCase
{
    public function test_create_delegates_to_repository(): void
    {
        $dto = new FakeDTO(null, 'Luigi');
        $repo = new FakeRepository();

        $stateful = new StatefulDTORepository($repo, $dto);
        $model = $stateful->create();

        $this->assertInstanceOf(Model::class, $model);
        $this->assertSame('Luigi', $model->name);
    }
}
