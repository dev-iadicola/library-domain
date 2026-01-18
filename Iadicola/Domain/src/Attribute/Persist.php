<?php
namespace Iadicola\Domain\Attribute;

use Attribute;
/**
 * Marks a DTO property as persistable.
 *
 * This attribute defines how a DTO property is mapped to a database
 * column when the DTO is converted to a persistence-ready array.
 *
 * Persistence semantics:
 * - Only properties annotated with this attribute are included
 *   in the persistence array.
 * - The `$column` value defines the target database column name.
 * - If `$nullable` is set to `true` and the property value is `null`,
 *   the resulting persistence array will explicitly contain
 *   `column => null`, causing the database column to be updated to NULL.
 * - If `$nullable` is set to `false` (default) and the property value
 *   is `null`, the property is skipped and the corresponding database
 *   column is left untouched.
 *
 * This distinction allows DTOs to differentiate between:
 * - "unset" properties (not included in the persistence array)
 * - and explicitly nullified properties (included as NULL)
 *
 * This attribute is intended for Laravel Eloquent persistence only.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Persist {
    public function __construct(
        public string $column, 
        public bool $nullable = false,
    )
    {
    }
}