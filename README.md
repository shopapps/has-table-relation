# HasTable and BelongsToTable relationships for Laravel Eloquent 
Provides a relation to an entire table without the need for any linked foreign key attributes.

This package provides a way to link a model to an entire table without the need for any foreign key attributes.  This is useful when you have a table that contains data that should be related to a model but does not have a foreign key column to link it to.

Whilst I would always recommend to have a local_key -> foreign_key link (e.g. `numbers.customer_id` => `customer.id`) for your related data allowing you to use the standard  hasMany / belongsTo relationships. 

Sometimes you just don't have the luxury of properly designed data models. :-(


# installation

```bash
composer require shopapps/has-table-relation
```

## HasTable relationship 
### Example
In this example the given Customer Model has all the records in the `numbers` table related to it.  Normally you would expect to see a related foreign key column such as `customer_id` in the `numbers` table to achieve this and use a hasMany relationship, however in my data this does not exist and due to the sensitive nature of the data I cannot add an extra column to this table.

```php

use ShopApps\HasTableRelation\Illuminate\Database\Eloquent\Concerns\HasOtherRelationships;
use App\Models\Number;

class Customer extends Model
{
    use HasOtherRelationships;

    public function numbers()
    {
        return $this->hasTable(Number::class);
    }
}
```
You can now retrieve all records from the numbers table using:
```
$customer = Customer::find(1);

$customer->numbers;

$customer->numbers()->where( 'last_called', '=>', Carbon::now()->subDays(14) )->paginate();
```


## BelongsToTable relationship
### Example
The inverse of the hasTable relationship.  

In this example all records in the `numbers` table belong to the given `Customer` model.  Normally you would expect to see a related local key column such as `customer_id` in the `numbers` table to achieve this and use a belongsTo relationship, however in my data this does not exist and due to the sensitive nature of the data I cannot add an extra column to this table.

you can pass an optional second parameter to the belongsToTable method to specify the method to call on the parent model.  The default is `first` but you can also use `last` or `all` to call the corresponding method on the parent model query.

```php

use ShopApps\HasTableRelation\Illuminate\Database\Eloquent\Concerns\HasOtherRelationships;
use App\Models\Customer;

class Number extends Model
{
    use HasOtherRelationships;

    public function customer()
    {
        return $this->belongsToTable(Customer::class);
    }
    // equivelent to ...
    public function customer()
    {
        return $this->belongsToTable(Customer::class, 'first'); // calls $query->first() on the parent model
    }
    // another example...
    public function customer()
    {
        return $this->belongsToTable(Customer::class, 'last'); // calls $query->last() on the parent model
    }
    // another example...
    public function customer()
    {
        return $this->belongsToTable(Customer::class, 'all'); // calls $query->all() on the parent model
    }
}
```
You can now retrieve the parent record from any of the the number records using:
```
$number = Number::find(1);

$number->customer;

// or if you have more than one customer record owner of this number
$number->customer()->where( 'active', true )->paginate();

```


