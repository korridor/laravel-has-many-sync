# Laravel HasMany Sync

[![Latest Version on Packagist](https://img.shields.io/packagist/v/korridor/laravel-has-many-sync?style=flat-square)](https://packagist.org/packages/korridor/laravel-has-many-sync)
[![License](https://img.shields.io/packagist/l/korridor/laravel-has-many-sync?style=flat-square)](license.md)
[![Codecov](https://img.shields.io/codecov/c/github/korridor/laravel-has-many-sync?style=flat-square)](https://codecov.io/gh/korridor/laravel-has-many-sync)
[![TravisCI](https://img.shields.io/travis/korridor/laravel-has-many-sync?style=flat-square)](https://travis-ci.org/korridor/laravel-has-many-sync)
[![StyleCI](https://styleci.io/repos/202400425/shield)](https://styleci.io/repos/202400425)

**Note: This package is a fork of [alfa6661/laravel-hasmany-sync](https://github.com/alfa6661/laravel-hasmany-sync). I already added tests and I plan to add more feature in the future.** 

Allow sync method for Laravel Has Many Relationship.

## Installation

You can install the package via composer with following command:

```bash
composer require korridor/laravel-hasmany-sync
```

### Requirements

This package is tested for the following Laravel and PHP versions:

 - 9.* (PHP 8.0, 8.1)
 - 8.* (PHP 7.4, 8.0, 8.1)
 - 7.* (PHP 7.2, 7.3, 7.4)
 - 6.* (PHP 7.2, 7.3)

## Usage

### Setup HasMany Relation

```php
class Customer extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contacts()
    {
        return $this->hasMany(CustomerContact::class);
    }
}
```

You can access the sync method like this:

```php
$customer->contacts()->sync([
    [
        'id' => 1,
        'name' => 'Alfa',
        'phone_number' => '123',
    ],
    [
        'id' => null,
        'name' => 'Adhitya',
        'phone_number' => '234,
    ]
]);
```

The sync method accepts an array of data to place on the intermediate table. Any data that are not in the given array will be removed from the intermediate table. So, after this operation is complete, only the data in the given array will exist in the intermediate table:

#### Syncing without deleting

If you do not want to delete existing data, you may pass  false value to the second parameter in the sync method.

```php
$customer->contacts()->sync([
    [
        'id' => 1,
        'name' => 'Alfa',
        'phone_number' => '123',
    ],
    [
        'id' => null,
        'name' => 'Adhitya',
        'phone_number' => '234,
    ]
], false);
```


#### Example usage in the controller.

```php
class CustomersController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  CustomerRequest  $request
     * @param  Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        DB::transaction(function () use ($customer, $request) {
            $customer->update($request->all());
            $customer->contacts()->sync($request->get('contacts', []));
        });

        return redirect()->route('customers.index');
    }
}
```

## Contributing

I am open for suggestions and contributions. Just create an issue or a pull request.

### Local docker environment

The `docker` folder contains a local docker environment for development.
The docker workspace has composer and xdebug installed.

```bash
docker-compose run workspace bash
```

### Testing

The `composer test` command runs all tests with [phpunit](https://phpunit.de/).
The `composer test-coverage` command runs all tests with phpunit and creates a coverage report into the `coverage` folder.

### Codeformatting/Linting

The `composer fix` command formats the code with [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer).
The `composer lint` command checks the code with [phpcs](https://github.com/squizlabs/PHP_CodeSniffer).

## License

This package is licensed under the MIT License (MIT). Please see [license file](license.md) for more information.

