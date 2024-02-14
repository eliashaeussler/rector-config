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

namespace EliasHaeussler\RectorConfig\Config;

use EliasHaeussler\RectorConfig\Entity;
use EliasHaeussler\RectorConfig\Exception;
use EliasHaeussler\RectorConfig\Set;
use Rector\Config as RectorConfig;
use Rector\Contract;
use Rector\Php73;
use Rector\Php74;
use Rector\PHPUnit;
use Rector\ValueObject;

use function array_values;
use function is_int;
use function is_string;

/**
 * Config.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class Config
{
    /**
     * @var list<Set\Set>
     */
    private array $sets;

    /**
     * @var list<non-empty-string>
     */
    private array $paths = [];

    /**
     * @var list<non-empty-string>
     */
    private array $skipPaths = [];

    /**
     * @var array<class-string<Contract\Rector\RectorInterface>, list<non-empty-string>>
     */
    private array $skippedRectors = [
        Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector::class => [],
        Php73\Rector\FuncCall\JsonThrowOnErrorRector::class => [],
        PHPUnit\CodeQuality\Rector\Class_\AddSeeTestAnnotationRector::class => [],
        PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector::class => [],
        PHPUnit\CodeQuality\Rector\ClassMethod\ReplaceTestAnnotationWithPrefixedFunctionRector::class => [],
    ];

    private function __construct(
        private readonly RectorConfig\RectorConfig $rectorConfig,
        private readonly Entity\Version $phpVersion,
    ) {
        $this->sets = [
            new Set\DefaultSet($this->phpVersion),
        ];
    }

    /**
     * @param Entity\Version|non-empty-string|positive-int|null $phpVersion
     *
     * @throws Exception\VersionStringIsInvalid
     */
    public static function create(
        RectorConfig\RectorConfig $rectorConfig,
        Entity\Version|string|int|null $phpVersion = null,
    ): self {
        $phpVersion ??= PHP_VERSION;

        if (is_string($phpVersion)) {
            $phpVersion = Entity\Version::createFromVersionString($phpVersion);
        }

        if (is_int($phpVersion)) {
            $phpVersion = Entity\Version::createFromVersionId($phpVersion);
        }

        return new self($rectorConfig, $phpVersion);
    }

    /**
     * @throws Exception\RequiredPackageIsMissing
     */
    public function withPHPUnit(?Entity\Version $version = null): self
    {
        $this->sets[] = new Set\PHPUnitSet($version);

        return $this;
    }

    /**
     * @throws Exception\RequiredPackageIsMissing
     */
    public function withSymfony(?Entity\Version $version = null): self
    {
        $this->sets[] = new Set\SymfonySet($version);

        return $this;
    }

    /**
     * @throws Exception\RequiredPackageIsMissing
     * @throws Exception\VersionStringIsInvalid
     */
    public function withTYPO3(?Entity\Version $version = null): self
    {
        $this->sets[] = new Set\TYPO3Set($version);

        return $this;
    }

    public function withSets(Set\Set ...$sets): self
    {
        foreach ($sets as $set) {
            $this->sets[] = $set;
        }

        return $this;
    }

    /**
     * @param non-empty-string ...$paths
     */
    public function in(string ...$paths): self
    {
        $this->paths = array_values($paths);

        return $this;
    }

    /**
     * @param non-empty-string ...$paths
     */
    public function not(string ...$paths): self
    {
        $this->skipPaths = array_values($paths);

        return $this;
    }

    /**
     * @param class-string<Contract\Rector\RectorInterface> $rector
     * @param list<non-empty-string>                        $paths
     */
    public function skip(string $rector, array $paths = [], bool $mergePaths = true): self
    {
        // Early return if no paths are configured
        if ([] === $paths) {
            $this->skippedRectors[$rector] = [];

            return $this;
        }

        // Merge path for configured Rector
        if ($mergePaths && isset($this->skippedRectors[$rector])) {
            $paths = [...$this->skippedRectors[$rector], ...$paths];
        }

        // Apply paths for configured Rector
        $this->skippedRectors[$rector] = $paths;

        return $this;
    }

    /**
     * Apply all rector configurations.
     */
    public function apply(): void
    {
        $skip = $this->skipPaths;

        // Transform skipped rectors for Rector config
        foreach ($this->skippedRectors as $skippedRector => $paths) {
            if ([] === $paths) {
                $skip[] = $skippedRector;
            } else {
                $skip[$skippedRector] = $paths;
            }
        }

        /** @var ValueObject\PhpVersion::* $phpVersionId */
        $phpVersionId = $this->phpVersion->toVersionId();

        $this->rectorConfig->phpVersion($phpVersionId);
        $this->rectorConfig->paths($this->paths);
        $this->rectorConfig->skip($skip);

        foreach ($this->sets as $set) {
            $this->rectorConfig->sets($set->get());
        }
    }
}
