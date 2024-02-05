<?php

declare(strict_types=1);

namespace DevMadeIt\Boiler\Schema;

use Illuminate\Support\Collection;

final class ModelSchemaCollection extends Collection
{
    public function __construct(array $items = [])
    {
        $this->items = collect($items)->map(fn ($item): ColumnSchema => ColumnSchema::fromArray((array) $item))->toArray();
    }

    public static function fromArray(array $items = []): static
    {
        return new self($items);
    }
}
