<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/rector-config".
 *
 * Copyright (C) 2023 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace EliasHaeussler\RectorConfig\Set;

use function array_diff;
use function array_values;

/**
 * CustomSet.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class CustomSet implements Set
{
    /**
     * @var list<non-empty-string>
     */
    private array $sets = [];

    /**
     * @param non-empty-string ...$sets
     */
    public function __construct(string ...$sets)
    {
        $this->add(...$sets);
    }

    /**
     * @param non-empty-string ...$sets
     */
    public function add(string ...$sets): self
    {
        $this->sets = array_values([...$this->sets, ...$sets]);

        return $this;
    }

    /**
     * @param non-empty-string ...$sets
     */
    public function remove(string ...$sets): self
    {
        $this->sets = array_values(array_diff($this->sets, $sets));

        return $this;
    }

    public function get(): array
    {
        return array_values($this->sets);
    }
}
