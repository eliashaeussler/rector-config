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

/**
 * CustomSetTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Set\CustomSet::class)]
final class CustomSetTest extends Framework\TestCase
{
    private Src\Set\CustomSet $subject;

    protected function setUp(): void
    {
        $this->subject = new Src\Set\CustomSet('foo', 'baz');
    }

    #[Framework\Attributes\Test]
    public function addAddsGivenSetsToCustomSet(): void
    {
        $this->subject->add('another foo', 'another baz');

        $expected = [
            'foo',
            'baz',
            'another foo',
            'another baz',
        ];

        self::assertSame($expected, $this->subject->get());
    }

    #[Framework\Attributes\Test]
    public function removeRemovesGivenSetsFromCustomSet(): void
    {
        $this->subject->remove('foo');

        $expected = [
            'baz',
        ];

        self::assertSame($expected, $this->subject->get());
    }

    #[Framework\Attributes\Test]
    public function getReturnsConfiguredSets(): void
    {
        $expected = [
            'foo',
            'baz',
        ];

        self::assertSame($expected, $this->subject->get());
    }
}
