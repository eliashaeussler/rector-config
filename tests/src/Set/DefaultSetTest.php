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

namespace EliasHaeussler\RectorConfig\Tests\Set;

use EliasHaeussler\RectorConfig as Src;
use PHPUnit\Framework;
use Rector\Set;

use function defined;
use function sprintf;

/**
 * DefaultSetTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Set\DefaultSet::class)]
final class DefaultSetTest extends Framework\TestCase
{
    private Src\Set\DefaultSet $subject;

    protected function setUp(): void
    {
        $this->subject = new Src\Set\DefaultSet();
    }

    #[Framework\Attributes\Test]
    public function constructorAllowsConfiguringPHPVersion(): void
    {
        $version = Src\Entity\Version::createFromVersionString('8.2.0');
        $subject = new Src\Set\DefaultSet($version);

        $actual = $subject->get();

        self::assertCount(2, $actual);
        self::assertMatchesRegularExpression('/config\\/set\\/level\\/up-to-php82\\.php$/', $actual[1]);
    }

    #[Framework\Attributes\Test]
    public function getReturnsDefaultSetWithLevelSetList(): void
    {
        $levelSetList = sprintf(
            '%s::UP_TO_PHP_%d%d',
            Set\ValueObject\LevelSetList::class,
            PHP_MAJOR_VERSION,
            PHP_MINOR_VERSION,
        );

        if (!defined($levelSetList)) {
            self::markTestSkipped('Level set list does not exist for this PHP version.');
        }

        $actual = $this->subject->get();

        self::assertCount(2, $actual);
        self::assertMatchesRegularExpression(
            sprintf('/config\\/set\\/level\\/up-to-php%d%d\\.php$/', PHP_MAJOR_VERSION, PHP_MINOR_VERSION),
            $actual[1],
        );
    }
}
