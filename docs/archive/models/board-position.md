# Scrapped codes in Board Position model 


## determineCategory 
### 1.0: static version
```php 
class BoardPosition extends Model
{
    use HasFactory;

    public static function determineCategory(string $name): string
    {
        $name = strtolower($name);

        if (str_contains($name, ['managing editor', 'editor-in-chief', 'associate editor', 'assoc. managing editor', 'circulation manager'])) {
            return 'executive';
        }

        if (str_contains($name, ['copy editor', 'news editor', 'feature editor', 'literary editor', 'community editor', 'sports editor'])) {
            return 'writers (editor)';
        }

        if (str_contains($name, ['head artist', 'graphics and layout artist', 'photojournalist'])) {
            return 'artists (editor)';
        }

        if (str_contains($name, ['writer', 'reporter'])) {
            return 'writers (staff)';
        }

        return 'uncategorized';
    }
}
```
