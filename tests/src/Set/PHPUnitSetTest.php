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

namespace EliasHaeussler\RectorConfig\Tests\Set;

use EliasHaeussler\RectorConfig as Src;
use PHPUnit\Framework;
use PHPUnit\Runner;
use Rector\PHPUnit;

use function defined;
use function sprintf;

/**
 * PHPUnitSetTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Set\PHPUnitSet::class)]
final class PHPUnitSetTest extends Framework\TestCase
{
    private Src\Set\PHPUnitSet $subject;

    protected function setUp(): void
    {
        $this->subject = new Src\Set\PHPUnitSet();
    }

    #[Framework\Attributes\Test]
    public function constructorAllowsConfiguringPHPUnitVersion(): void
    {
        $version = Src\Entity\Version::createFromVersionString('9.5.0');
        $subject = new Src\Set\PHPUnitSet($version);

        $actual = $subject->get();

        self::assertCount(3, $actual);
        self::assertStringEndsWith('/config/sets/phpunit90.php', $actual[2]);
    }

    #[Framework\Attributes\Test]
    public function getReturnsPHPUnitSetWithSetList(): void
    {
        $phpUnitVersion = Runner\Version::majorVersionNumber();

        $actual = $this->subject->get();

        if ($this->phpUnitSetListExists($phpUnitVersion)) {
            self::assertCount(4, $actual);
            self::assertMatchesRegularExpression(
                sprintf('/config\\/sets\\/phpunit%d\\d\\.php$/', $phpUnitVersion),
                $actual[3],
            );
        } else {
            self::assertCount(3, $actual);
        }
    }

    private function phpUnitSetListExists(int $phpUnitVersion): bool
    {
        return defined(sprintf('%s::PHPUNIT_%d0', PHPUnit\Set\PHPUnitSetList::class, $phpUnitVersion));
    }
}
