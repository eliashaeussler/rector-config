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

namespace EliasHaeussler\RectorConfig\Tests\Helper;

use EliasHaeussler\RectorConfig as Src;
use PHPUnit\Framework;
use Rector\PHPUnit;
use Rector\Symfony;

/**
 * VersionHelperTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Helper\VersionHelper::class)]
final class VersionHelperTest extends Framework\TestCase
{
    #[Framework\Attributes\Test]
    public function getRectorSetListForPackageForPackageReturnsSetList(): void
    {
        $expected = PHPUnit\Set\PHPUnitSetList::PHPUNIT_100;

        self::assertSame(
            $expected,
            Src\Helper\VersionHelper::getRectorSetListForPackage(
                Src\Entity\Version::createFromVersionString('10.0.13'),
                PHPUnit\Set\PHPUnitSetList::class,
                'PHPUNIT_%d',
            ),
        );
    }

    #[Framework\Attributes\Test]
    public function getRectorSetListForPackageForPackageReturnsNullOnInvalidSetList(): void
    {
        self::assertNull(
            Src\Helper\VersionHelper::getRectorSetListForPackage(
                Src\Entity\Version::createFromVersionString('1.0.0'),
                PHPUnit\Set\PHPUnitSetList::class,
                'PHPUNIT_%d',
            ),
        );
    }

    #[Framework\Attributes\Test]
    public function getRectorSetListForPackageForPackageReturnsSetListForPreviousVersion(): void
    {
        /* @phpstan-ignore classConstant.deprecatedClass */
        $expected = Symfony\Set\SymfonySetList::SYMFONY_54;

        self::assertSame(
            $expected,
            Src\Helper\VersionHelper::getRectorSetListForPackage(
                Src\Entity\Version::createFromVersionString('5.99.99'),
                /* @phpstan-ignore classConstant.deprecatedClass */
                Symfony\Set\SymfonySetList::class,
                'SYMFONY_%d',
                Src\Enums\VersionRange::MajorMinor,
                true,
            ),
        );
    }

    #[Framework\Attributes\Test]
    public function getRectorSetListForPackageForPackageReturnsNUllIfNoPreviousVersionExists(): void
    {
        self::assertNull(
            Src\Helper\VersionHelper::getRectorSetListForPackage(
                Src\Entity\Version::createFromVersionString('2.1.0'),
                PHPUnit\Set\PHPUnitSetList::class,
                'PHPUNIT_%d',
                Src\Enums\VersionRange::MajorMinor,
                true,
            ),
        );
    }
}
