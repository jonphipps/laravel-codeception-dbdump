Laravel Codeception DbDump
==========================

_Create database dumps ready to be used with Codeception_

Codeception comes with a nice [Db module](http://codeception.com/docs/modules/Db) that keeps the test database clean
before each test is run. To use it you are expected to put a database dump in `tests/_data` directory. This is a major
pain, because each time the database changes, a new dump is required. This package adds a single command to your
project that will migrate, seed and dump a test database making it ready to be used in Codeception tests.

## Installation

Install through composer:

```
composer require antennaio/laravel-codeception-dbdump
```

Add the service provider to config:

```
// config/app.php
'provider' => [
    ...
    Antennaio\Codeception\DbDumpServiceProvider::class,
    ...
];
```

## Usage

Run the command with a single argument `connection`, which is one of the connections defined in `app/database.php`.

```
php artisan codeception:dbdump connection
```

## Run the command whenever your tests run

It's possible to have this command run automatically before any of your tests are executed by adding
it to `tests/_bootstrap.php`:

```
exec('php artisan codeception:dbdump connection');
```

## Compatibility

The `codeception:dbdump` command is currently compatible with MySQL and SQLite drivers.
