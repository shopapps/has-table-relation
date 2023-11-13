# HasTable and BelongsToTable relationships for Laravel Eloquent 
Provides a relation to an entire table without the need for any linked foreign key attributes.

This package provides a way to link a model to an entire table without the need for any foreign key attributes.  This is useful when you have a table that contains data that should be related to a model but does not have a foreign key column to link it to.

Whilst I would always recommend to have a local_key -> foreign_key link (e.g. `numbers.customer_id` => `customer.id`) for your related data allowing you to use the standard  hasMany / belongsTo relationships. 

Sometimes you just don't have the luxury of properly designed data models. :-(

#### NOTE: before usign this package, you could also consider using a `belongsToMany` relationship and create a pivot table between the two tables.  Make sure if you do that you then seed the pivot table mapping all the records you need and also keep them in sync when your code add's removes records.  I have added a sample migration file at the bottom of this readme that would allow you to do this as per my customer -> numbers example scenario.

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
    public function customers() // plural since we will be returning more than one ;-) 
    {
        return $this->belongsToTable(Customer::class, 'all'); // calls $query->get() on the parent model
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


# BelongsToMany - A laravel standard relationship
Before usign this package, you could also consider using a `belongsToMany` relationship and create a pivot table between the two tables.  Make sure if you do that you then seed the pivot table mapping all the records you need.  I have added a sample migration file below that would do this as per my customer -> numbers example scenario.

```php

<?php

use App\Models\Customer;
use App\Models\Number;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table     = 'customer_number'; // standard is to keep the table names in alphabetical order
    public $columnOne = 'customer';
    public $columnTwo = 'number';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->table))
        {
            Schema::create($this->table, function (Blueprint $table)
            {
                $table->unsignedInteger("{$this->columnOne}_id")->index();
                $table->unsignedInteger("{$this->columnTwo}_id")->index();

                /* setup index across the two */
                $table->primary(["{$this->columnOne}_id", "{$this->columnTwo}_id"]);
            });

            $this->buildData();  // populate the pivot table
        }
    }

    public function buildData() {

        /*
         * find the first record in $columnOne and populate the pivot table with all records from $columnTwo
         */

        $connection = Schema::getConnection();

        /** @var Customer $customer */
        $customer = new Customer();
        $customer->setConnection($connection->getName());
        $customer = $customer->first(); // my table only has one record, yours may be different, so collect and loop accordingly.

        if($customer) {
            /** @var Number $customer */
            $numbers = new Number();
            $numbers->setConnection($connection->getName());
            $numbers = $numbers->get(); // get all the rows

            if(count($numbers) > 0) {
                $numbers = $numbers->pluck('id')->toArray(); // put id's into an array
                $customer->numbers()->syncWithoutDetaching($numbers); // attach the id's to the pivot table
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};

```

