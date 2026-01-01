<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/rector-config".
 *
 * Copyright (C) 2023-2026 Elias Häußler <elias@haeussler.dev>
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

use EliasHaeussler\RectorConfig\Entity;
use EliasHaeussler\RectorConfig\Enums;
use EliasHaeussler\RectorConfig\Exception;
use EliasHaeussler\RectorConfig\Helper;
use Rector\PHPUnit;

/**
 * PHPUnitSet.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final readonly class PHPUnitSet implements Set
{
    private Entity\Version $phpUnitVersion;

    /**
     * @throws Exception\RequiredPackageIsMissing
     * @throws Exception\VersionStringIsInvalid
     */
    public function __construct(?Entity\Version $version = null)
    {
        $this->phpUnitVersion = $version ?? Entity\Version::createFromInstalledPackage('phpunit/phpunit');
    }

    public function get(): array
    {
        $phpUnit10 = Entity\Version::createMajor(10);
        $set = [
            PHPUnit\Set\PHPUnitSetList::PHPUNIT_60,
            PHPUnit\Set\PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        ];

        // Add PHPUnit 10.x sets
        if ($this->phpUnitVersion->isGreaterThan($phpUnit10) || $this->phpUnitVersion->equals($phpUnit10)) {
            $set[] = PHPUnit\Set\PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES;
        }

        // Determine set list
        $setList = Helper\VersionHelper::getRectorSetListForPackage(
            $this->phpUnitVersion,
            PHPUnit\Set\PHPUnitSetList::class,
            'PHPUNIT_%d',
            Enums\VersionRange::MajorDotZero,
        );

        // Add level set list, if available
        if (null !== $setList) {
            $set[] = $setList;
        }

        return $set;
    }
}
