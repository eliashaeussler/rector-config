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

namespace EliasHaeussler\RectorConfig\Tests\Config;

use EliasHaeussler\RectorConfig as Src;
use EliasHaeussler\RectorConfig\Tests;
use Generator;
use PHPUnit\Framework;
use Rector\CodeQuality;
use Rector\Config;
use Rector\Configuration;
use Rector\Php73;
use Rector\Php74;
use Rector\PHPUnit;
use Rector\Set;
use Rector\Symfony;
use Rector\ValueObject;
use Ssch\TYPO3Rector;

use function count;

/**
 * ConfigTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Config\Config::class)]
final class ConfigTest extends Framework\TestCase
{
    use Tests\RectorConfigTrait;

    #[Framework\Attributes\Test]
    public function createReturnsDefaultConfigObjectWithoutApplyingIt(): void
    {
        $rectorConfig = $this->createRectorConfig();

        Src\Config\Config::create($rectorConfig);

        self::assertSame([], Configuration\Parameter\SimpleParameterProvider::provideArrayParameter(Configuration\Option::SKIP));
    }

    /**
     * @param non-empty-string|positive-int $phpVersion
     * @param positive-int                  $expected
     */
    #[Framework\Attributes\Test]
    #[Framework\Attributes\DataProvider('createCanHandleCustomPHPVersionDataProvider')]
    public function createCanHandleCustomPHPVersion(string|int $phpVersion, int $expected): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) use ($phpVersion) {
                $subject = Src\Config\Config::create($rectorConfig, $phpVersion);
                $subject->apply();
            },
        );

        self::assertSame(
            $expected,
            Configuration\Parameter\SimpleParameterProvider::provideIntParameter(Configuration\Option::PHP_VERSION_FEATURES),
        );
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
                PHPUnit\CodeQuality\Rector\Class_\AddSeeTestAnnotationRector::class,
                PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector::class,
            ],
            Configuration\Parameter\SimpleParameterProvider::provideArrayParameter(Configuration\Option::SKIP),
        );
    }

    #[Framework\Attributes\Test]
    public function withPHPUnitRespectsGivenVersion(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->withPHPUnit(Src\Entity\Version::createMinor(9, 5));
                $subject->apply();
            },
        );

        /* @see PHPUnit\Set\PHPUnitSetList::PHPUNIT_90 */
        self::assertTrue($this->container?->has(PHPUnit\PHPUnit90\Rector\Class_\TestListenerToHooksRector::class));
        /* @see PHPUnit\Set\PHPUnitSetList::PHPUNIT_100 */
        self::assertFalse($this->container->has(PHPUnit\PHPUnit100\Rector\Class_\StaticDataProviderClassMethodRector::class));
    }

    #[Framework\Attributes\Test]
    public function withPHPUnitImportsPHPUnitSetListInRectorConfig(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->withPHPUnit();
                $subject->apply();
            },
        );

        /* @see PHPUnit\Set\PHPUnitSetList::PHPUNIT_100 */
        self::assertTrue($this->container?->has(PHPUnit\PHPUnit100\Rector\Class_\StaticDataProviderClassMethodRector::class));
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
        self::assertTrue($this->container?->has(PHPUnit\AnnotationsToAttributes\Rector\ClassMethod\DataProviderAnnotationToAttributeRector::class));
    }

    #[Framework\Attributes\Test]
    public function withSymfonyRespectsGivenVersion(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->withSymfony(Src\Entity\Version::createMajor(6));
                $subject->apply();
            },
        );

        /* @see Symfony\Set\SymfonySetList::SYMFONY_60 */
        self::assertTrue($this->container?->has(Symfony\Symfony60\Rector\MethodCall\GetHelperControllerToServiceRector::class));
        /* @see Symfony\Set\SymfonySetList::SYMFONY_62 */
        self::assertFalse($this->container->has(Symfony\Symfony62\Rector\MethodCall\SimplifyFormRenderingRector::class));
    }

    #[Framework\Attributes\Test]
    public function withSymfonyImportsSymfonySetListInRectorConfig(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->withSymfony(Src\Entity\Version::createMinor(6, 2));
                $subject->apply();
            },
        );

        /* @see Symfony\Set\SymfonySetList::SYMFONY_62 */
        self::assertTrue($this->container?->has(Symfony\Symfony62\Rector\MethodCall\SimplifyFormRenderingRector::class));
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
        self::assertTrue($this->container?->has(Symfony\Symfony42\Rector\MethodCall\ContainerGetToConstructorInjectionRector::class));
    }

    #[Framework\Attributes\Test]
    public function withTYPO3RespectsGivenVersion(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->withTYPO3(Src\Entity\Version::createMajor(11));
                $subject->apply();
            },
        );

        /* @see TYPO3Rector\Set\Typo3SetList::TYPO3_11 */
        self::assertTrue($this->container?->has(TYPO3Rector\Rector\v11\v0\ForwardResponseInsteadOfForwardMethodRector::class));
        /* @see TYPO3Rector\Set\Typo3SetList::TYPO3_12 */
        self::assertFalse($this->container->has(TYPO3Rector\Rector\v12\v0\tca\MigrateColsToSizeForTcaTypeNoneRector::class));
    }

    #[Framework\Attributes\Test]
    public function withTYPO3AddsAdditionalTYPO3RulesToRectorConfig(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->withTYPO3();
                $subject->apply();
            },
        );

        /* @see TYPO3Rector\Set\Typo3SetList::TYPO3_12 */
        self::assertTrue($this->container?->has(TYPO3Rector\Rector\v12\v0\tca\MigrateColsToSizeForTcaTypeNoneRector::class));
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
                $subject->in('foo/*', 'baz/*');
                $subject->apply();
            },
        );

        self::assertSame(
            ['foo/*', 'baz/*'],
            Configuration\Parameter\SimpleParameterProvider::provideArrayParameter(Configuration\Option::PATHS),
        );
    }

    #[Framework\Attributes\Test]
    public function notSkipsAllGivenPathsInRectorConfig(): void
    {
        $this->createRectorConfig(
            static function (Config\RectorConfig $rectorConfig) {
                $subject = Src\Config\Config::create($rectorConfig);
                $subject->not('foo', 'baz');
                $subject->apply();
            },
        );

        $actual = Configuration\Parameter\SimpleParameterProvider::provideArrayParameter(Configuration\Option::SKIP);

        self::assertGreaterThanOrEqual(2, count($actual));
        self::assertSame('foo', $actual[0]);
        self::assertSame('baz', $actual[1]);
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
                PHPUnit\CodeQuality\Rector\Class_\AddSeeTestAnnotationRector::class,
                PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector::class,
                Php74\Rector\Closure\ClosureToArrowFunctionRector::class,
            ],
            Configuration\Parameter\SimpleParameterProvider::provideArrayParameter(Configuration\Option::SKIP),
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
                PHPUnit\CodeQuality\Rector\Class_\AddSeeTestAnnotationRector::class,
                PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector::class,
                Php74\Rector\Closure\ClosureToArrowFunctionRector::class => ['foo', 'baz'],
            ],
            Configuration\Parameter\SimpleParameterProvider::provideArrayParameter(Configuration\Option::SKIP),
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
                PHPUnit\CodeQuality\Rector\Class_\AddSeeTestAnnotationRector::class,
                PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector::class,
                Php74\Rector\Closure\ClosureToArrowFunctionRector::class => ['foo', 'baz'],
            ],
            Configuration\Parameter\SimpleParameterProvider::provideArrayParameter(Configuration\Option::SKIP),
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
                PHPUnit\CodeQuality\Rector\Class_\AddSeeTestAnnotationRector::class,
                PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector::class,
                Php74\Rector\Closure\ClosureToArrowFunctionRector::class => ['baz'],
            ],
            Configuration\Parameter\SimpleParameterProvider::provideArrayParameter(Configuration\Option::SKIP),
        );
    }

    /**
     * @return Generator<string, array{non-empty-string|positive-int, positive-int}>
     */
    public static function createCanHandleCustomPHPVersionDataProvider(): Generator
    {
        yield 'version string' => ['8.2.0', ValueObject\PhpVersion::PHP_82];
        yield 'version id' => [ValueObject\PhpVersion::PHP_82, ValueObject\PhpVersion::PHP_82];
    }

    protected function tearDown(): void
    {
        Tests\RectorConfigInvoker::reset();
    }
}
