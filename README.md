# repo

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

A Laravel package for lightweight route localization. 
This package will set the app repo based on the request. 
E.g. `/nl/foo` will set repo to `nl`. 

## Install

Via Composer

``` bash
$ composer require thinktomorrow/repo
```

Next add the provider to the providers array in the `/config/app.php` file:

``` php
    Thinktomorrow\Repo\RepoServiceProvider::class,
```

Finally create a configuration file to `/config/thinktomorrow/repo.php`

``` bash
    php artisan vendor:publish --provider="Thinktomorrow\Repo\RepoServiceProvider"
```

Not required, but if you want to use a facade you can add in the `config/app.php` file as well:

``` php
'aliases' => [
    ...
    'Repo' => 'Thinktomorrow\Repo\RepoFacade',
    'RepoUrl' => 'Thinktomorrow\Repo\RepoUrlFacade',
];
```

## Usage

To make your routes localized, place them inside a Route::group() with a prefix value that is determined by the Repo class itself. 
To avoid possible conflicts with your deployments, you should call the `Thinktomorrow\Repo\Repo` class via the `app()` container instead of the facade inside the `routes.php` file.

``` php
    
    Route::group(['prefix' => app(Thinktomorrow\Repo\Repo::class)->set()],function(){
        
        // Routes registered within this group will be localized
        
    });
    
```
**Note**: *Subdomain- and tld-based localization should be possible as well but this is currently not fully supported yet.*

## Generating a localized url

Localisation of your routes is done automatically when <a href="https://laravel.com/docs/5.2/routing#named-routes" target="_blank">named routes</a> are being used. 
Creation of all named routes will be localized based on current repo. Quick non-obtrusive integration. 

``` php
    route('pages.about'); // prints out http://example.com/en/about (if en is the active repo)
```

To create an url with a specific repo other than the active one, you can use the `Thinktomorrow\Repo\RepoUrl` class.

``` php
    // Generate localized url from uri (resolves as laravel url() function)
    Thinktomorrow\Repo\RepoUrl::to('about','en'); // prints out http://example.com/en/about
    
    // Generate localized url from named route (resolves as laravel route() function)
    Thinktomorrow\Repo\RepoUrl::route('pages.about','en'); // prints out http://example.com/en/about  
```

**Note:** Passing the repo as 'lang' query parameter will force the repo 
*example.com/en/about?lang=nl* makes sure the request will deal with a 'nl' repo.

## Configuration
- **available_repos**: Whitelist of repos available for usage inside your application. 
- **hidden_repo**: You can set one of the available repos as 'hidden' which means any request without a repo in its uri, should be localized as this hidden repo.
For example if the hidden repo is 'nl' and the request uri is /foo/bar, this request is interpreted with the 'nl' repo. 
Note that this is best used for your main / default repo.

## Repo API

#### Set a new repo for current request
``` php
    app('Thinktomorrow\Repo\Repo')->set('en');
```

#### Get the current repo
``` php
    app('Thinktomorrow\Repo\Repo')->get(); // returns 'en' and is basically an alias for app()->getRepo();
```

## Changing repo
This is an example on how you allow an user to change the repo. In this case the route `/lang?repo=en` will
set the new repo to `en` and returns to the user's current page in the new repo.

``` php

// /app/Http/routes.php:
Route::get('lang',['as' => 'lang.switch','uses' => LanguageSwitcher::class.'@store']);

// /app/Http/Controllers/LanguageSwitcher.php:
namespace App\Http\Controllers;

use URL, Illuminate\Http\Request;
use Repo, RepoUrl;

class LanguageSwitcher extends Controller
{
    public function store(Request $request)
    {
        $repo = $request->get('repo');
        
        // Set new repo
        Repo::set($repo);
        
        // Get current visited page and return to it in the new repo
        $previous = RepoUrl::to(URL::previous(),Repo::get());
        
        return redirect()->to($previous);
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