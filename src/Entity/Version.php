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

namespace EliasHaeussler\RectorConfig\Entity;

use Composer\InstalledVersions;
use EliasHaeussler\RectorConfig\Enums;
use EliasHaeussler\RectorConfig\Exception;
use OutOfBoundsException;
use Stringable;

use function sprintf;
use function version_compare;

/**
 * Version.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final readonly class Version implements Stringable
{
    private const VERSION_PATTERN = '/^v?(?P<major>\\d+)(?:\\.(?P<minor>\\d+)(?:\\.(?P<patch>\\d+))?)?/';

    private function __construct(
        public int $major,
        public int $minor = 0,
        public int $patch = 0,
    ) {}

    public static function createMajor(int $major): self
    {
        return new self($major);
    }

    public static function createMinor(int $major, int $minor): self
    {
        return new self($major, $minor);
    }

    public static function createPatch(int $major, int $minor, int $patch): self
    {
        return new self($major, $minor, $patch);
    }

    /**
     * @throws Exception\RequiredPackageIsMissing
     * @throws Exception\VersionStringIsInvalid
     */
    public static function createFromInstalledPackage(string $packageName): self
    {
        try {
            $version = InstalledVersions::getPrettyVersion($packageName);
        } catch (OutOfBoundsException) {
            $version = null;
        }

        if (null === $version || '' === $version) {
            throw new Exception\RequiredPackageIsMissing($packageName);
        }

        return self::createFromVersionString($version);
    }

    /**
     * @throws Exception\VersionStringIsInvalid
     */
    public static function createFromVersionString(string $versionString): self
    {
        if (1 !== preg_match(self::VERSION_PATTERN, $versionString, $matches)) {
            throw new Exception\VersionStringIsInvalid($versionString);
        }

        $major = (int) $matches['major'];
        $minor = (int) ($matches['minor'] ?? 0);
        $patch = (int) ($matches['patch'] ?? 0);

        return new self($major, $minor, $patch);
    }

    /**
     * @param positive-int $versionId
     */
    public static function createFromVersionId(int $versionId): self
    {
        $major = (int) round($versionId / 10000, 0, PHP_ROUND_HALF_DOWN);
        $minor = (int) round(($versionId - $major * 10000) / 100, 0, PHP_ROUND_HALF_DOWN);
        $patch = $versionId - $major * 10000 - $minor * 100;

        return new self($major, $minor, $patch);
    }

    public function toString(): string
    {
        return sprintf('%d.%d.%d', $this->major, $this->minor, $this->patch);
    }

    public function toVersionId(): int
    {
        return ($this->major * 10000) + ($this->minor * 100) + $this->patch;
    }

    public function range(Enums\VersionRange $range): string
    {
        return match ($range) {
            Enums\VersionRange::MajorMinor => $this->major.$this->minor,
            Enums\VersionRange::MajorDotZero => $this->major.'0',
            Enums\VersionRange::MajorOnly => (string) $this->major,
        };
    }

    public function compareTo(self $other): int
    {
        return version_compare($this->toString(), $other->toString());
    }

    public function isLowerThan(self $other): bool
    {
        return $this->compareTo($other) < 0;
    }

    public function equals(self $other): bool
    {
        return 0 === $this->compareTo($other);
    }

    public function isGreaterThan(self $other): bool
    {
        return $this->compareTo($other) > 0;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
