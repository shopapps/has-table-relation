# has-table-relation
provides a relation to an entire table without the need for any linked attributes

# installation

```bash
composer require shop apps/has-table-relation
```

# usage
Use the `HasTable` trait in your Eloquent model:
```php
use ShopApps\HasTableRelation\HasTable;

class YourModel extends Model
{
    use HasTable;

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
