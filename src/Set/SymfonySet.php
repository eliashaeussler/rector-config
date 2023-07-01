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

use EliasHaeussler\RectorConfig\Enums;
use EliasHaeussler\RectorConfig\Exception;
use EliasHaeussler\RectorConfig\Helper;
use Rector\Symfony;

/**
 * SymfonySet.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class SymfonySet implements Set
{
    private const POSSIBLE_PACKAGES = [
        'symfony/symfony',
        'symfony/config',
        'symfony/console',
        'symfony/dependency-injection',
        'symfony/process',
        'symfony/runtime',
    ];

    /**
     * @var non-empty-string
     */
    private readonly string $symfonyVersion;

    /**
     * @throws Exception\MissingRequiredPackageException
     */
    public function __construct()
    {
        $this->symfonyVersion = $this->determineSymfonyVersion();
    }

    public function get(): array
    {
        $set = [
            Symfony\Set\SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
            Symfony\Set\SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
            Symfony\Set\SymfonySetList::SYMFONY_50_TYPES,
        ];

        // Determine level set list
        $levelSetList = Helper\VersionHelper::getRectorLevelSetListForPackage(
            $this->symfonyVersion,
            Symfony\Set\SymfonyLevelSetList::class,
            'UP_TO_SYMFONY_%d',
            Enums\VersionRange::MajorMinor,
            true,
        );

        // Add level set list, if available
        if (null !== $levelSetList) {
            $set[] = $levelSetList;
        }

        return $set;
    }

    /**
     * @return non-empty-string
     *
     * @throws Exception\MissingRequiredPackageException
     */
    private function determineSymfonyVersion(): string
    {
        foreach (self::POSSIBLE_PACKAGES as $packageName) {
            try {
                return Helper\VersionHelper::getPackageVersion($packageName);
            } catch (Exception\MissingRequiredPackageException) {
                // Continue with next package.
            }
        }

        // @codeCoverageIgnoreStart
        throw Exception\MissingRequiredPackageException::create('Symfony');
        // @codeCoverageIgnoreEnd
    }
}
