# Scrapped codes in the AppServiceProvider 

## route binding
### 1.0: where(...)->firstOrFail()
This should only be used when we're matching not by primary key or with more complex conditions 
```php 
Route::bind('article', function (string $value) {
    return Article::withTrashed()->where('id', $value)->firstOrFail(); # for forceDestroy & restore
});
Route::bind('multimedia', function (string $value) {
    return Multimedia::withTrashed()->where('id', $value)->firstOrFail();
});
Route::bind('community-segment', function (string $value) {
    return CommunitySegment::withTrashed()->where('id', $value)->firstOrFail();
})
Route::bind('bulletin', function (string $value) {
    return Bulletin::withTrashed()->where('id', $value)->firstOrFail();
})
Route::bind('issue', function (string $value) {
    return Issue::withTrashed()->where('id', $value)->firstOrFail();
})
Route::bind('board-position', function (string $value) {
    return BoardPosition::withTrashed()->where('id', $value)->firstOrFail();
})
Route::bind('user', function (string $value) {
    return User::withTrashed()->where('id', $value)->firstOrFail();
});
```
