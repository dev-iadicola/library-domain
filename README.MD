# iadicola/domain


**DTO-driven domain persistence layer for Laravel**

`iadicola/domain` is a Laravel-oriented library that introduces a **DTO-first approach to data persistence**, making Data Transfer Objects the **single source of truth** for create, update, and upsert operations on Eloquent models.

The goal of this library is to make persistence **explicit, intentional, and testable**, avoiding anonymous arrays and hidden model mutations spread across the codebase.
---

# Project status

Initial version: 0.1.0

Public API may evolve

Tooling (Artisan commands) is not included in the core package
---
##  Tests

The project is covered by unit tests based on PHPUnit.

To run the full test suite, simply execute:

```bash
composer test
```
The command uses TestDox format with colored output to clearly display
which classes and methods are being tested.
--- 

## Goals

- Centralize persistence logic using DTOs
- Avoid raw arrays in repositories and services
- Make create / update / upsert flows explicit
- Reduce tight coupling between application code and Eloquent models
- Improve readability, predictability, and testability

---

##  What this library is NOT

- Not an ORM
-  Not a replacement for Eloquent
-  Not a full DDD or Hexagonal Architecture framework
-  Not responsible for validation or business logic

This library focuses **only** on **DTO-driven persistence**.

---

##  Package structure


---

##  Core concepts

###  Data Transfer Objects (DTO)

DTOs represent **persistable data**, not Eloquent models.

They:
- Expose only writable attributes
- Define unique keys via `unique()`
- Filter attributes according to the model `fillable`
- Act as the **single source of truth** for persistence

All DTOs must implement `IDTO` and extend `BaseDTO`.

---

### DTO-driven repositories

Repositories **never accept raw arrays**.

The data flow is always explicit: DTO → Repository → Model


This prevents hidden mutations and unclear persistence behavior.

---

##  DTO example

```php
final class UserDTO extends BaseDTO
{
    public function __construct(
        ?int $id,
        public string $name,
        public string $email
    ) {
        parent::__construct($id);
    }

    public static function fromModel(Model $model): static
    {
        return new static(
            $model->id,
            $model->name,
            $model->email
        );
    }

    public function unique(): array
    {
        return ['email' => $this->email];
    }
}
```
## Stateless usage (explicit)
```php
$dto = UserDTO::fromModel($user);

$repository = new BaseDTORepository(new User());

$repository->update($dto);
```
## Stateful usage (ergonomic)
```php
$dto = UserDTO::fromModel($user);

$repository = new StatefulDTORepository(
    new BaseDTORepository(new User()),
    $dto
);

$repository->update();
```

## Fast Usage 
```php
use App\Model\User
$dto = UsetDTO::fromArray($request->all())->repo(new User)->create();
```


