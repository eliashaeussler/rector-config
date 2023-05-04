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

namespace EliasHaeussler\RectorConfig\Helper;

use Composer\InstalledVersions;
use EliasHaeussler\RectorConfig\Enums;
use EliasHaeussler\RectorConfig\Exception;
use OutOfBoundsException;

use function defined;
use function explode;
use function is_string;
use function ltrim;
use function sprintf;

/**
 * VersionHelper.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class VersionHelper
{
    /**
     * @throws Exception\MissingRequiredPackageException
     */
    public static function getPackageVersion(string $packageName): string
    {
        try {
            $version = InstalledVersions::getPrettyVersion($packageName);
        } catch (OutOfBoundsException) {
            $version = null;
        }

        if (null === $version) {
            throw Exception\MissingRequiredPackageException::create($packageName);
        }

        return $version;
    }

    /**
     * @param class-string $levelSetList
     */
    public static function getRectorLevelSetListForPackage(
        string $packageVersion,
        string $levelSetList,
        string $constantPattern,
        Enums\VersionRange $versionRange = Enums\VersionRange::MajorMinor,
    ): ?string {
        [$major, $minor] = explode('.', $packageVersion, 3);

        $versionPattern = match ($versionRange) {
            Enums\VersionRange::MajorMinor => $major.$minor,
            Enums\VersionRange::MajorDotZero => $major.'0',
            Enums\VersionRange::MajorOnly => $major,
        };

        $constant = sprintf('%s::%s', $levelSetList, sprintf($constantPattern, ltrim($versionPattern, 'v')));
        $value = defined($constant) ? constant($constant) : null;

        if (is_string($value)) {
            return $value;
        }

        return null;
    }
}
