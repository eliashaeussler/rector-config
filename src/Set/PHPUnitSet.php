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

use EliasHaeussler\RectorConfig\Enums;
use EliasHaeussler\RectorConfig\Exception;
use EliasHaeussler\RectorConfig\Helper;
use Rector\PHPUnit;

use function version_compare;

/**
 * PHPUnitSet.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class PHPUnitSet implements Set
{
    /**
     * @var non-empty-string
     */
    private readonly string $phpUnitVersion;

    /**
     * @throws Exception\MissingRequiredPackageException
     */
    public function __construct()
    {
        $this->phpUnitVersion = Helper\VersionHelper::getPackageVersion('phpunit/phpunit');
    }

    public function get(): array
    {
        $set = [
            PHPUnit\Set\PHPUnitSetList::PHPUNIT_EXCEPTION,
            PHPUnit\Set\PHPUnitSetList::PHPUNIT_SPECIFIC_METHOD,
            PHPUnit\Set\PHPUnitSetList::PHPUNIT_YIELD_DATA_PROVIDER,
        ];

        // Add PHPUnit 10.x sets
        if (version_compare($this->phpUnitVersion, '10.0.0', '>=')) {
            $set[] = PHPUnit\Set\PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES;
        }

        // Determine level set list
        $levelSetList = Helper\VersionHelper::getRectorLevelSetListForPackage(
            $this->phpUnitVersion,
            PHPUnit\Set\PHPUnitLevelSetList::class,
            'UP_TO_PHPUNIT_%d',
            Enums\VersionRange::MajorDotZero,
        );

        // Add level set list, if available
        if (null !== $levelSetList) {
            $set[] = $levelSetList;
        }

        return $set;
    }
}
