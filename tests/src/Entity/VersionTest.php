<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/rector-config".
 *
 * Copyright (C) 2023-2024 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\RectorConfig\Tests\Entity;

use EliasHaeussler\RectorConfig as Src;
use Generator;
use PHPUnit\Framework;

/**
 * VersionTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Entity\Version::class)]
final class VersionTest extends Framework\TestCase
{
    private Src\Entity\Version $subject;

    protected function setUp(): void
    {
        $this->subject = Src\Entity\Version::createFromVersionString('1.2.3');
    }

    #[Framework\Attributes\Test]
    public function createMajorReturnsMajorVersion(): void
    {
        $actual = Src\Entity\Version::createMajor(1);

        self::assertSame(1, $actual->major);
        self::assertSame(0, $actual->minor);
        self::assertSame(0, $actual->patch);
    }

    #[Framework\Attributes\Test]
    public function createMinorReturnsMinorVersion(): void
    {
        $actual = Src\Entity\Version::createMinor(1, 2);

        self::assertSame(1, $actual->major);
        self::assertSame(2, $actual->minor);
        self::assertSame(0, $actual->patch);
    }

    #[Framework\Attributes\Test]
    public function createPatchReturnsPatchVersion(): void
    {
        $actual = Src\Entity\Version::createPatch(1, 2, 3);

        self::assertSame(1, $actual->major);
        self::assertSame(2, $actual->minor);
        self::assertSame(3, $actual->patch);
    }

    #[Framework\Attributes\Test]
    public function createFromInstalledPackageThrowsExceptionIfPackageIsNotInstalled(): void
    {
        $this->expectExceptionObject(
            new Src\Exception\RequiredPackageIsMissing('foo'),
        );

        Src\Entity\Version::createFromInstalledPackage('foo');
    }

    #[Framework\Attributes\Test]
    public function createFromInstalledPackageReturnsInstalledPackageVersion(): void
    {
        $actual = Src\Entity\Version::createFromInstalledPackage('phpunit/phpunit');

        self::assertSame(10, $actual->major);
    }

    #[Framework\Attributes\Test]
    public function createFromVersionStringThrowsExceptionIfVersionStringIsInvalid(): void
    {
        $this->expectExceptionObject(
            new Src\Exception\VersionStringIsInvalid('foo'),
        );

        Src\Entity\Version::createFromVersionString('foo');
    }

    #[Framework\Attributes\Test]
    #[Framework\Attributes\DataProvider('createFromVersionStringConvertsVersionStringToVersionDataProvider')]
    public function createFromVersionStringConvertsVersionStringToVersion(
        string $versionString,
        Src\Entity\Version $expected,
    ): void {
        self::assertEquals($expected, Src\Entity\Version::createFromVersionString($versionString));
    }

    #[Framework\Attributes\Test]
    public function createFromVersionIdConvertsVersionIdToVersion(): void
    {
        $actual = Src\Entity\Version::createFromVersionId(PHP_VERSION_ID);

        self::assertSame(PHP_MAJOR_VERSION, $actual->major);
        self::assertSame(PHP_MINOR_VERSION, $actual->minor);
        self::assertSame(PHP_RELEASE_VERSION, $actual->patch);
    }

    #[Framework\Attributes\Test]
    public function toStringReturnsFullVersion(): void
    {
        self::assertSame('1.2.3', $this->subject->toString());
    }

    #[Framework\Attributes\Test]
    public function toVersionIdReturnsVersionId(): void
    {
        self::assertSame(10203, $this->subject->toVersionId());
    }

    #[Framework\Attributes\Test]
    #[Framework\Attributes\DataProvider('rangeReturnsVersionStringForGivenRangeDataProvider')]
    public function rangeReturnsVersionStringForGivenRange(Src\Enums\VersionRange $range, string $expected): void
    {
        self::assertSame($expected, $this->subject->range($range));
    }

    #[Framework\Attributes\Test]
    #[Framework\Attributes\DataProvider('compareToReturnsVersionComparisonDataProvider')]
    public function compareToReturnsVersionComparison(Src\Entity\Version $other, int $expected): void
    {
        self::assertSame($expected, $this->subject->compareTo($other));
    }

    #[Framework\Attributes\Test]
    public function isLowerThanReturnsTrueIfVersionIsLowerThanGivenVersion(): void
    {
        self::assertTrue($this->subject->isLowerThan(Src\Entity\Version::createMajor(2)));
        self::assertFalse($this->subject->isLowerThan(Src\Entity\Version::createMajor(1)));
    }

    #[Framework\Attributes\Test]
    public function equalsReturnsTrueIfVersionEqualsGivenVersion(): void
    {
        self::assertTrue($this->subject->equals(Src\Entity\Version::createFromVersionString('1.2.3')));
        self::assertFalse($this->subject->equals(Src\Entity\Version::createMajor(1)));
    }

    #[Framework\Attributes\Test]
    public function isGreaterThanReturnsTrueIfVersionIsGreaterThanGivenVersion(): void
    {
        self::assertFalse($this->subject->isGreaterThan(Src\Entity\Version::createMajor(2)));
        self::assertTrue($this->subject->isGreaterThan(Src\Entity\Version::createMajor(1)));
    }

    #[Framework\Attributes\Test]
    public function stringRepresentationReturnsFullVersion(): void
    {
        self::assertSame('1.2.3', (string) $this->subject);
    }

    /**
     * @return Generator<string, array{string, Src\Entity\Version}>
     */
    public static function createFromVersionStringConvertsVersionStringToVersionDataProvider(): Generator
    {
        yield 'major only' => ['1', Src\Entity\Version::createMajor(1)];
        yield 'major with prefix' => ['v1', Src\Entity\Version::createMajor(1)];
        yield 'minor' => ['1.2', Src\Entity\Version::createMinor(1, 2)];
        yield 'minor with prefix' => ['v1.2', Src\Entity\Version::createMinor(1, 2)];
        yield 'patch' => ['1.2.3', Src\Entity\Version::createPatch(1, 2, 3)];
        yield 'patch with prefix' => ['v1.2.3', Src\Entity\Version::createPatch(1, 2, 3)];
    }

    /**
     * @return Generator<string, array{Src\Enums\VersionRange, string}>
     */
    public static function rangeReturnsVersionStringForGivenRangeDataProvider(): Generator
    {
        yield 'major and minor' => [Src\Enums\VersionRange::MajorMinor, '12'];
        yield 'major dot zero' => [Src\Enums\VersionRange::MajorDotZero, '10'];
        yield 'major only' => [Src\Enums\VersionRange::MajorOnly, '1'];
    }

    /**
     * @return Generator<string, array{Src\Entity\Version, int}>
     */
    public static function compareToReturnsVersionComparisonDataProvider(): Generator
    {
        yield 'lower' => [Src\Entity\Version::createFromVersionString('1.3.0'), -1];
        yield 'equal' => [Src\Entity\Version::createFromVersionString('1.2.3'), 0];
        yield 'greater' => [Src\Entity\Version::createFromVersionString('1.1.0'), 1];
    }
}
