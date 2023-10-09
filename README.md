# has-table-relation
provides a relation to an entire table without the need for any linked attributes

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
