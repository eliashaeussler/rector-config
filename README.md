<div align="center">

# Rector config

[![CGL](https://github.com/eliashaeussler/rector-config/actions/workflows/cgl.yaml/badge.svg)](https://github.com/eliashaeussler/rector-config/actions/workflows/cgl.yaml)
[![Tests](https://github.com/eliashaeussler/rector-config/actions/workflows/tests.yaml/badge.svg)](https://github.com/eliashaeussler/rector-config/actions/workflows/tests.yaml)
[![Release](https://github.com/eliashaeussler/rector-config/actions/workflows/release.yaml/badge.svg)](https://github.com/eliashaeussler/rector-config/actions/workflows/release.yaml)
[![Latest Stable Version](http://poser.pugx.org/eliashaeussler/rector-config/v)](https://packagist.org/packages/eliashaeussler/rector-config)
[![PHP Version Require](http://poser.pugx.org/eliashaeussler/rector-config/require/php)](https://packagist.org/packages/eliashaeussler/rector-config)
[![License](http://poser.pugx.org/eliashaeussler/rector-config/license)](LICENSE)

</div>

This package contains basic [Rector](https://github.com/rectorphp/rector)
config for use in my personal projects. It is not meant to be used anywhere else.
I won't provide support and don't accept pull requests for this repo.

## 🔥 Installation

```bash
composer require --dev eliashaeussler/rector-config
```

## ⚡ Usage

```php
# rector.php

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import('vendor/eliashaeussler/rector-config/rector.dist.php');

    $rectorConfig->paths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ]);

    $rectorConfig->phpVersion(PhpVersion::PHP_81);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_81,
    ]);
};
```

## ⭐ License

This project is licensed under [GNU General Public License 3.0 (or later)](LICENSE).
