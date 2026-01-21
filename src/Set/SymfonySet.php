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
use Rector\Symfony;

/**
 * SymfonySet.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final readonly class SymfonySet implements Set
{
    private const POSSIBLE_PACKAGES = [
        'symfony/symfony',
        'symfony/config',
        'symfony/console',
        'symfony/dependency-injection',
        'symfony/process',
        'symfony/runtime',
    ];

    private Entity\Version $symfonyVersion;

    /**
     * @throws Exception\RequiredPackageIsMissing
     * @throws Exception\VersionStringIsInvalid
     */
    public function __construct(?Entity\Version $version = null)
    {
        $this->symfonyVersion = $version ?? $this->determineSymfonyVersion();
    }

    public function get(): array
    {
        $set = [
            /* @phpstan-ignore classConstant.deprecated */
            Symfony\Set\SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
            Symfony\Set\SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
            /* @phpstan-ignore classConstant.deprecated */
            Symfony\Set\SymfonySetList::SYMFONY_50_TYPES,
        ];

        // Determine set list
        $setList = Helper\VersionHelper::getRectorSetListForPackage(
            $this->symfonyVersion,
            Symfony\Set\SymfonySetList::class,
            'SYMFONY_%d',
            Enums\VersionRange::MajorMinor,
            true,
        );

        // Add level set list, if available
        if (null !== $setList) {
            $set[] = $setList;
        }

        return $set;
    }

    /**
     * @throws Exception\RequiredPackageIsMissing
     * @throws Exception\VersionStringIsInvalid
     */
    private function determineSymfonyVersion(): Entity\Version
    {
        foreach (self::POSSIBLE_PACKAGES as $packageName) {
            try {
                return Entity\Version::createFromInstalledPackage($packageName);
            } catch (Exception\RequiredPackageIsMissing) {
                // Continue with next package.
            }
        }

        // @codeCoverageIgnoreStart
        throw new Exception\RequiredPackageIsMissing('Symfony');
        // @codeCoverageIgnoreEnd
    }
}
