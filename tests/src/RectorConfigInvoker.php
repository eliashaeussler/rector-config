<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/rector-config".
 *
 * Copyright (C) 2023-2025 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\RectorConfig\Tests;

use Rector\Config;
use Rector\Configuration;
use ReflectionClass;

use function array_pop;

/**
 * RectorConfigInvoker.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 *
 * @internal
 */
final class RectorConfigInvoker
{
    /**
     * @var list<callable(Config\RectorConfig): mixed>
     */
    private static array $callables = [];

    /**
     * @param callable(Config\RectorConfig): mixed $callable
     */
    public static function push(callable $callable): void
    {
        self::$callables[] = $callable;
    }

    public static function call(Config\RectorConfig $rectorConfig): void
    {
        while ($callable = array_pop(self::$callables)) {
            $callable($rectorConfig);
        }
    }

    public static function reset(): void
    {
        self::$callables = [];

        // Reset container parameters
        $reflection = new ReflectionClass(Configuration\Parameter\SimpleParameterProvider::class);
        $reflection->setStaticPropertyValue('parameters', []);
    }
}
