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

namespace EliasHaeussler\RectorConfig\Set;

use EliasHaeussler\RectorConfig\Entity;
use EliasHaeussler\RectorConfig\Enums;
use EliasHaeussler\RectorConfig\Exception;
use EliasHaeussler\RectorConfig\Helper;
use Ssch\TYPO3Rector;
use TYPO3\CMS\Core;

/**
 * TYPO3Set.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final readonly class TYPO3Set implements Set
{
    private Entity\Version $typo3Version;

    /**
     * @throws Exception\RequiredPackageIsMissing
     * @throws Exception\VersionStringIsInvalid
     */
    public function __construct(?Entity\Version $version = null)
    {
        // @codeCoverageIgnoreStart
        if (!class_exists(Core\Information\Typo3Version::class)) {
            throw new Exception\RequiredPackageIsMissing('typo3/cms-core');
        }
        if (!class_exists(TYPO3Rector\Set\Typo3LevelSetList::class)) {
            throw new Exception\RequiredPackageIsMissing('ssch/typo3-rector');
        }
        // @codeCoverageIgnoreEnd

        $this->typo3Version = $version ?? Entity\Version::createFromVersionString(
            (new Core\Information\Typo3Version())->getVersion(),
        );
    }

    public function get(): array
    {
        $set = [];

        // Determine level set list
        $levelSetList = Helper\VersionHelper::getRectorSetListForPackage(
            $this->typo3Version,
            TYPO3Rector\Set\Typo3LevelSetList::class,
            'UP_TO_TYPO3_%d',
            Enums\VersionRange::MajorOnly,
        );

        // Add level set list, if available
        if (null !== $levelSetList) {
            $set[] = $levelSetList;
        }

        return $set;
    }
}
