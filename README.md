# Laravel HasMany Sync

Allow sync method for Laravel Has Many Relationship.

# Installation

You can install the package via composer:

```
composer require alfa/laravel-has-many-sync
```

Register the ServiceProvider in `config/app.php`

```php
'providers' => [
    // ...
    Alfa\EloquentHasManySync\ServiceProvider::class,
],
```

# Usage


## Setup HasMany Relation

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

### Syncing without deleting

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


### Example usage in the controller.

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