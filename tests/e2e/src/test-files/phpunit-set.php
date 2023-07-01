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

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * This class should convert annotations to attributes.
 *
 * @see \Rector\PHPUnit\Set\PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES
 */
final class PHPUnitAnnotationToAttributesSetListTestClass extends TestCase
{
    /**
     * @test
     */
    public function testCase(): void
    {
    }
}

/**
 * This class should convert exception annotations to methods.
 *
 * @see \Rector\PHPUnit\Set\PHPUnitSetList::PHPUNIT_EXCEPTION
 */
final class PHPUnitExceptionSetListTestClass extends TestCase
{
    /**
     * @expectedException \Exception
     */
    #[Test]
    public function testCase(): void
    {
    }
}

/**
 * This class should convert specific PHPUnit methods.
 *
 * @see \Rector\PHPUnit\Set\PHPUnitSetList::PHPUNIT_SPECIFIC_METHOD
 */
final class PHPUnitSpecificMethodSetListTestClass extends TestCase
{
    #[Test]
    public function testCase(): void
    {
        $foo = false;

        self::assertTrue(!$foo);
    }
}
