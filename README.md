<div align="center">

# Rector config

[![Coverage](https://img.shields.io/codecov/c/github/eliashaeussler/rector-config?logo=codecov&token=YcuK5zoSWw)](https://codecov.io/gh/eliashaeussler/rector-config)
[![Maintainability](https://img.shields.io/codeclimate/maintainability/eliashaeussler/rector-config?logo=codeclimate)](https://codeclimate.com/github/eliashaeussler/rector-config/maintainability)
[![CGL](https://img.shields.io/github/actions/workflow/status/eliashaeussler/rector-config/cgl.yaml?label=cgl&logo=github)](https://github.com/eliashaeussler/rector-config/actions/workflows/cgl.yaml)
[![Tests](https://img.shields.io/github/actions/workflow/status/eliashaeussler/rector-config/tests.yaml?label=tests&logo=github)](https://github.com/eliashaeussler/rector-config/actions/workflows/tests.yaml)
[![Supported PHP Versions](https://img.shields.io/packagist/dependency-v/eliashaeussler/rector-config/php?logo=php)](https://packagist.org/packages/eliashaeussler/rector-config)

</div>

This package contains basic [Rector](https://github.com/rectorphp/rector)
config for use in my personal projects. It is not meant to be used anywhere else.
I won't provide support and don't accept pull requests for this repo.

## 🔥 Installation

[![Packagist](https://img.shields.io/packagist/v/eliashaeussler/rector-config?label=version&logo=packagist)](https://packagist.org/packages/eliashaeussler/rector-config)
[![Packagist Downloads](https://img.shields.io/packagist/dt/eliashaeussler/rector-config?color=brightgreen)](https://packagist.org/packages/eliashaeussler/rector-config)

```bash
composer require --dev eliashaeussler/rector-config
```

## ⚡ Usage

```php
# rector.php

use EliasHaeussler\RectorConfig\Config\Config;
use EliasHaeussler\RectorConfig\Set\CustomSet;
use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $config = Config::create($rectorConfig)->in(
        __DIR__.'/src',
        __DIR__.'/tests',
    );

    // Skip specific paths
    $config->not(
        __DIR__.'/src/lib',
        __DIR__.'/tests/test-application/vendor',
    );

    // Include default PHPUnit sets
    $config->withPHPUnit();

    // Include default Symfony sets
    $config->withSymfony();

    // Include default TYPO3 sets
    $config->withTYPO3();

    // Include custom sets
    $config->withSets(
        new CustomSet(
            SetList::CODE_QUALITY,
            SetList::CODING_STYLE,
        ),
    );

    // Skip specific rectors
    $config->skip(
        AnnotationToAttributeRector::class,
        [
            __DIR__.'/src/Some/File.php',
        ],
    );

    // Apply configuration
    $config->apply();
};
```

## ⭐ License

This project is licensed under [GNU General Public License 3.0 (or later)](LICENSE).
