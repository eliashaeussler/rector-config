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

namespace EliasHaeussler\RectorConfig\Helper;

use EliasHaeussler\RectorConfig\Entity;
use EliasHaeussler\RectorConfig\Enums;

use function defined;
use function is_string;
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
     * @param class-string     $setList
     * @param non-empty-string $constantPattern
     *
     * @return non-empty-string|null
     */
    public static function getRectorSetListForPackage(
        Entity\Version $packageVersion,
        string $setList,
        string $constantPattern,
        Enums\VersionRange $versionRange = Enums\VersionRange::MajorMinor,
        bool $fallBackToPreviousVersions = false,
    ): ?string {
        $normalizedVersion = Entity\Version::createMinor($packageVersion->major, $packageVersion->minor);

        $constant = sprintf(
            '%s::%s',
            $setList,
            sprintf($constantPattern, $normalizedVersion->range($versionRange)),
        );
        $value = defined($constant) ? constant($constant) : null;

        if (is_string($value) && '' !== $value) {
            return $value;
        }

        if (!$fallBackToPreviousVersions) {
            return null;
        }

        $previousVersion = self::resolvePreviousVersion($normalizedVersion, $versionRange);

        if (!$normalizedVersion->equals($previousVersion)) {
            return self::getRectorSetListForPackage(
                $previousVersion,
                $setList,
                $constantPattern,
                $versionRange,
                true,
            );
        }

        return null;
    }

    private static function resolvePreviousVersion(
        Entity\Version $packageVersion,
        Enums\VersionRange $versionRange,
    ): Entity\Version {
        return match ($versionRange) {
            Enums\VersionRange::MajorMinor => Entity\Version::createMinor(
                $packageVersion->major,
                max($packageVersion->minor - 1, 0),
            ),
            Enums\VersionRange::MajorDotZero, Enums\VersionRange::MajorOnly => Entity\Version::createMinor(
                max($packageVersion->major - 1, 0),
                $packageVersion->minor,
            ),
        };
    }
}
