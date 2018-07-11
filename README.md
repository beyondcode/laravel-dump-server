# Laravel Dump Server

[![Latest Version on Packagist](https://img.shields.io/packagist/v/beyondcode/laravel-dump-server.svg?style=flat-square)](https://packagist.org/packages/beyondcode/laravel-dump-server)
[![Quality Score](https://img.shields.io/scrutinizer/g/beyondcode/laravel-dump-server.svg?style=flat-square)](https://scrutinizer-ci.com/g/beyondcode/laravel-dump-server)
[![Total Downloads](https://img.shields.io/packagist/dt/beyondcode/laravel-dump-server.svg?style=flat-square)](https://packagist.org/packages/beyondcode/laravel-dump-server)

Bringing the [Symfony Var-Dump Server](https://symfony.com/doc/current/components/var_dumper.html#the-dump-server) to Laravel.

This package will give you a dump server, that collects all your `dump` call outputs, so that it does not interfere with HTTP / API responses.

## Installation

You can install the package via composer:

```bash
composer require --dev beyondcode/laravel-dump-server
```

The package will register itself automatically. 

Optionally you can publish the package configuration using:

```bash
php artisan vendor:publish --provider=BeyondCode\\DumpServer\\DumpServerServiceProvider
```

This will publish a file called `debug-server.php` in your `config` folder.
In the config file, you can specify the dump server host that you want to listen on, in case you want to change the default value.

## Usage

Start the dump server by calling the artisan command:

```bash
php artisan dump-server
```

You can set the output format to HTML using the `--format` option:

```bash
php artisan dump-server --format=html > dump.html
```

And then you can, as you are used to, put `dump` calls in your methods. But instead of dumping the output in your current HTTP request, they will be dumped in the artisan command.
This is very useful, when you want to dump data from API requests, without having to deal with HTTP errors.

You can see it in action here:

![Dump Server Demo](https://beyondco.de/github/dumpserver/dumpserver.gif)

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email marcel@beyondco.de instead of using the issue tracker.

## Credits

- [Marcel Pociot](https://github.com/mpociot)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
 
