# Scrapped codes in the ArticleFactory 


## images transform 
```php 
'cover_photo' => $this->cover_photo
    ? (filter_var($this->cover_photo, FILTER_VALIDATE_URL)
        ? $this->cover_photo  // It's already a full URL, use as-is
        : Storage::url($this->cover_photo))  // It's a local path, add /storage/
    : null,
'thumbnail' => $this->thumbnail
    ? (filter_var($this->cover_photo, FILTER_VALIDATE_URL)
        ? $this->thumbnail
        : Storage::url($this->thumbnail))
    : null,
```
