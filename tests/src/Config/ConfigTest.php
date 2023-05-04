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

namespace EliasHaeussler\RectorConfig\Tests\Config;

use EliasHaeussler\RectorConfig as Src;
use EliasHaeussler\RectorConfig\Tests;
use PHPUnit\Framework;
use Rector\CodeQuality;
use Rector\Config;
use Rector\Core;
use Rector\Php73;
use Rector\Php74;
use Rector\PHPUnit;
use Rector\Set;
use Rector\Symfony;

/**
 * ConfigTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class ConfigTest extends Framework\TestCase
{
    use Tests\RectorConfigTrait;

    #[Framework\Attributes\Test]
    public function createReturnsDefaultConfigObjectWithoutApplyingIt(): void
    {
        $rectorConfig = $this->createRectorConfig();

        Src\Config\Config::create($rectorConfig);

        self::assertSame([], $this->container?->getParameter(Core\Configuration\Option::SKIP));
    }

    #[Framework\Attributes\Test]
    public function applyConfiguresDefaultParametersInRectorConfig(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->apply();
            },
        );

        self::assertSame(
            [
                Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector::class,
                Php73\Rector\FuncCall\JsonThrowOnErrorRector::class,
            ],
            $this->container?->getParameter(Core\Configuration\Option::SKIP),
        );
    }

    #[Framework\Attributes\Test]
    public function withPHPUnitImportsPHPUnitLevelSetListInRectorConfig(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->withPHPUnit();
                $subject->apply();
            },
        );

        /* @see PHPUnit\Set\PHPUnitLevelSetList::UP_TO_PHPUNIT_100 */
        self::assertTrue($this->container?->has(PHPUnit\Rector\Class_\StaticDataProviderClassMethodRector::class));
    }

    #[Framework\Attributes\Test]
    public function withPHPUnitAddsAdditionalPHPUnitRulesToRectorConfig(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->withPHPUnit();
                $subject->apply();
            },
        );

        /* @see PHPUnit\Set\PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES */
        self::assertTrue($this->container?->has(PHPUnit\Rector\ClassMethod\DataProviderAnnotationToAttributeRector::class));
    }

    #[Framework\Attributes\Test]
    public function withSymfonyImportsSymfonyLevelSetListInRectorConfig(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->withSymfony();
                $subject->apply();
            },
        );

        /* @see Symfony\Set\SymfonyLevelSetList::UP_TO_SYMFONY_62 */
        self::assertTrue($this->container?->has(Symfony\Rector\MethodCall\SimplifyFormRenderingRector::class));
    }

    #[Framework\Attributes\Test]
    public function withSymfonyAddsAdditionalSymfonyRulesToRectorConfig(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->withSymfony();
                $subject->apply();
            },
        );

        /* @see Symfony\Set\SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION */
        self::assertTrue($this->container?->has(Symfony\Rector\MethodCall\ContainerGetToConstructorInjectionRector::class));
    }

    #[Framework\Attributes\Test]
    public function withSetsAddsCustomSetsToRectorConfig(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->withSets(
                    new Src\Set\CustomSet(
                        Set\ValueObject\SetList::CODE_QUALITY,
                    ),
                );
                $subject->apply();
            },
        );

        /* @see Set\ValueObject\SetList::CODE_QUALITY */
        self::assertTrue($this->container?->has(CodeQuality\Rector\BooleanAnd\SimplifyEmptyArrayCheckRector::class));
    }

    #[Framework\Attributes\Test]
    public function inConfiguresAllGivenPathsInRectorConfig(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->in('foo', 'baz');
                $subject->apply();
            },
        );

        self::assertSame(['foo', 'baz'], $this->container?->getParameter(Core\Configuration\Option::PATHS));
    }

    #[Framework\Attributes\Test]
    public function skipSkipsGivenRectorInAllPaths(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->skip(Php74\Rector\Closure\ClosureToArrowFunctionRector::class);
                $subject->apply();
            },
        );

        self::assertSame(
            [
                Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector::class,
                Php73\Rector\FuncCall\JsonThrowOnErrorRector::class,
                Php74\Rector\Closure\ClosureToArrowFunctionRector::class,
            ],
            $this->container?->getParameter(Core\Configuration\Option::SKIP),
        );
    }

    #[Framework\Attributes\Test]
    public function skipSkipsGivenRectorInConfiguredPaths(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->skip(Php74\Rector\Closure\ClosureToArrowFunctionRector::class, ['foo', 'baz']);
                $subject->apply();
            },
        );

        self::assertSame(
            [
                Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector::class,
                Php73\Rector\FuncCall\JsonThrowOnErrorRector::class,
                Php74\Rector\Closure\ClosureToArrowFunctionRector::class => ['foo', 'baz'],
            ],
            $this->container?->getParameter(Core\Configuration\Option::SKIP),
        );
    }

    #[Framework\Attributes\Test]
    public function skipMergesPathsOfGivenRector(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->skip(Php74\Rector\Closure\ClosureToArrowFunctionRector::class, ['foo']);
                $subject->skip(Php74\Rector\Closure\ClosureToArrowFunctionRector::class, ['baz']);
                $subject->apply();
            },
        );

        self::assertSame(
            [
                Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector::class,
                Php73\Rector\FuncCall\JsonThrowOnErrorRector::class,
                Php74\Rector\Closure\ClosureToArrowFunctionRector::class => ['foo', 'baz'],
            ],
            $this->container?->getParameter(Core\Configuration\Option::SKIP),
        );
    }

    #[Framework\Attributes\Test]
    public function skipOverwritesPathsOfGivenRector(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->skip(Php74\Rector\Closure\ClosureToArrowFunctionRector::class, ['foo']);
                $subject->skip(Php74\Rector\Closure\ClosureToArrowFunctionRector::class, ['baz'], false);
                $subject->apply();
            },
        );

        self::assertSame(
            [
                Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector::class,
                Php73\Rector\FuncCall\JsonThrowOnErrorRector::class,
                Php74\Rector\Closure\ClosureToArrowFunctionRector::class => ['baz'],
            ],
            $this->container?->getParameter(Core\Configuration\Option::SKIP),
        );
    }

    protected function tearDown(): void
    {
        Tests\RectorConfigInvoker::reset();
    }
}
