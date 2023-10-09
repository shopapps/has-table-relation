# has-table-relation
provides a relation to an entire table without the need for any linked attributes.  Wierd use case i know, but in my requirement, we had multi-tenant data grouped by database, but we also had a sync'd copy of some of this data in a global database. so we had:
```
Customers -> Numbers
```
In the same database e.g. db_name = `tenant_1` and Customers only actually had one record (I know... i know... but this was inherited data which could not be changed) so all the numbers in the `Numbers` table belonged to that customer

Then we had a separate `Estate` database which had `Customers` and `Numbers` but related in the standard way.


# installation

```bash
composer require shopapps/has-table-relation
```

# usage
Use the `HasTable` trait in your Eloquent model:
```php
use ShopApps\HasTableRelation\Illuminate\Database\Eloquent\Concerns\HasOtherRelationships;

class YourModel extends Model
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
$yourModelInstance->numbers;

$yourModelInstance->numbers()->where('column','=', 'test')->paginate();
```
