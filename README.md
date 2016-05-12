# repo

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

A Laravel repository package for retrieving, filtering and sorting models

## Install

Via Composer

``` bash
$ composer require thinktomorrow/repo
```

## Usage

``` php
    
<?php

namespace App\Domain;

use Thinktomorrow\Repo\BaseRepository;
use Thinktomorrow\Repo\Filterable;
use Thinktomorrow\Repo\Sortable;

class ChildRepository extends BaseRepository{

    use Filterable, Sortable;

    public function __construct(ModelStub $model)
    {
        $this->setModel($model);
    }
}
    
```

## Testing

``` bash
$ vendor/bin/phpunit
```

## Security

If you discover any security related issues, please email ben@thinktomorrow.be instead of using the issue tracker.

## Credits

- Ben Cavens <ben@thinktomorrow.be>

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/thinktomorrow/repo.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/thinktomorrow/repo/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/thinktomorrow/repo.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/thinktomorrow/repo.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/thinktomorrow/repo.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/thinktomorrow/repo
[link-travis]: https://travis-ci.org/thinktomorrow/repo
[link-scrutinizer]: https://scrutinizer-ci.com/g/thinktomorrow/repo/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/thinktomorrow/repo
[link-downloads]: https://packagist.org/packages/thinktomorrow/repo
[link-author]: https://github.com/bencavens