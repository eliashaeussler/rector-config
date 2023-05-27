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

namespace EliasHaeussler\RectorConfig\Set;

use EliasHaeussler\RectorConfig\Helper;
use Rector\Set as RectorSet;

/**
 * DefaultSet.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class DefaultSet implements Set
{
    /**
     * @param non-empty-string $phpVersion
     */
    public function __construct(
        private readonly string $phpVersion = PHP_VERSION,
    ) {
    }

    public function get(): array
    {
        $set = [
            RectorSet\ValueObject\SetList::PRIVATIZATION,
        ];

        // Determine level set list
        $levelSetList = Helper\VersionHelper::getRectorLevelSetListForPackage(
            $this->phpVersion,
            RectorSet\ValueObject\LevelSetList::class,
            'UP_TO_PHP_%d',
        );

        // Add level set list, if available
        if (null !== $levelSetList) {
            $set[] = $levelSetList;
        }

        return $set;
    }
}
