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

use Illuminate\Container;
use Rector\Config;
use Rector\DependencyInjection;
use Rector\ValueObject;

/**
 * RectorConfigTrait.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
trait RectorConfigTrait
{
    /**
     * @var Container\Container|null
     */
    private ?object $container = null;

    /**
     * @param callable(Config\RectorConfig): (mixed)|null $configCallable
     */
    private function createRectorConfig(?callable $configCallable = null): Config\RectorConfig
    {
        $rectorConfig = null;

        RectorConfigInvoker::push(static function (Config\RectorConfig $currentRectorConfig) use (&$rectorConfig) {
            $rectorConfig = $currentRectorConfig;
        });

        if (null !== $configCallable) {
            RectorConfigInvoker::push($configCallable);
        }

        $bootstrapConfigs = new ValueObject\Bootstrap\BootstrapConfigs(__DIR__.'/Fixtures/rector.php', []);
        $containerFactory = new DependencyInjection\RectorContainerFactory();

        $this->container = $containerFactory->createFromBootstrapConfigs($bootstrapConfigs);

        self::assertInstanceOf(Config\RectorConfig::class, $rectorConfig);

        return $rectorConfig;
    }
}
