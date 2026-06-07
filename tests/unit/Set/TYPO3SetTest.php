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
use TYPO3\CMS\Core;

use function sprintf;

/**
 * TYPO3SetTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Set\TYPO3Set::class)]
final class TYPO3SetTest extends Framework\TestCase
{
    private Src\Set\TYPO3Set $subject;

    protected function setUp(): void
    {
        $this->subject = new Src\Set\TYPO3Set();
    }

    #[Framework\Attributes\Test]
    public function constructorAllowsConfiguringSymfonyVersion(): void
    {
        $version = Src\Entity\Version::createFromVersionString('11.5');
        $subject = new Src\Set\TYPO3Set($version);

        $actual = $subject->get();

        self::assertCount(1, $actual);
        self::assertStringEndsWith('/config/level/up-to-typo3-11.php', $actual[0]);
    }

    #[Framework\Attributes\Test]
    public function getReturnsTYPO3SetWithLevelSetList(): void
    {
        $actual = $this->subject->get();

        self::assertCount(1, $actual);
        self::assertMatchesRegularExpression(
            sprintf('/config\\/level\\/up-to-typo3-%d\\.php$/', (new Core\Information\Typo3Version())->getMajorVersion()),
            $actual[0],
        );
    }
}
