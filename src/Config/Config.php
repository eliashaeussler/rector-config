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

namespace EliasHaeussler\RectorConfig\Config;

use EliasHaeussler\RectorConfig\Set;
use Rector\Config as RectorConfig;
use Rector\Core;
use Rector\Php73;
use Rector\Php74;

use function array_values;

/**
 * Config.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class Config
{
    /**
     * @var list<non-empty-string>
     */
    private array $paths = [];

    /**
     * @var array<class-string<Core\Contract\Rector\RectorInterface>, list<string>>
     */
    private array $skippedRectors = [
        Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector::class => [],
        Php73\Rector\FuncCall\JsonThrowOnErrorRector::class => [],
    ];

    /**
     * @param list<Set\Set> $sets
     */
    private function __construct(
        private readonly RectorConfig\RectorConfig $rectorConfig,
        private array $sets,
    ) {
    }

    public static function create(RectorConfig\RectorConfig $rectorConfig): self
    {
        return new self($rectorConfig, [new Set\DefaultSet()]);
    }

    public function withPHPUnit(): self
    {
        $this->sets[] = new Set\PHPUnitSet();

        return $this;
    }

    public function withSymfony(): self
    {
        $this->sets[] = new Set\SymfonySet();

        return $this;
    }

    public function withTYPO3(): self
    {
        $this->sets[] = new Set\TYPO3Set();

        return $this;
    }

    public function withSets(Set\Set ...$sets): self
    {
        foreach ($sets as $set) {
            $this->sets[] = $set;
        }

        return $this;
    }

    /**
     * @param non-empty-string ...$paths
     */
    public function in(string ...$paths): self
    {
        $this->paths = array_values($paths);

        return $this;
    }

    /**
     * @param class-string<Core\Contract\Rector\RectorInterface> $rector
     * @param list<string>                                       $paths
     */
    public function skip(string $rector, array $paths = [], bool $mergePaths = true): self
    {
        // Early return if no paths are configured
        if ([] === $paths) {
            $this->skippedRectors[$rector] = [];

            return $this;
        }

        // Merge path for configured Rector
        if ($mergePaths && isset($this->skippedRectors[$rector])) {
            $paths = [...$this->skippedRectors[$rector], ...$paths];
        }

        // Apply paths for configured Rector
        $this->skippedRectors[$rector] = $paths;

        return $this;
    }

    /**
     * Apply all rector configurations.
     */
    public function apply(): void
    {
        $skip = [];

        // Transform skipped rectors for Rector config
        foreach ($this->skippedRectors as $skippedRector => $paths) {
            if ([] === $paths) {
                $skip[] = $skippedRector;
            } else {
                $skip[$skippedRector] = $paths;
            }
        }

        $this->rectorConfig->paths($this->paths);
        $this->rectorConfig->skip($skip);

        foreach ($this->sets as $set) {
            $this->rectorConfig->sets($set->get());
        }
    }
}
