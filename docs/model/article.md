# Article 

Documentation shall consist Article model's properties, resource and migrations

## Relationships 
1. category <-belongsTo-> ArticleCategory 
2. writer <-belongsTo-> User  
3. coverArtist <-belongsTo-> User
4. thumbnailArtist <-belongsTo-> User
5. self reference for live news:
   1. series -> Article 
   2. seriesArticles <-hasMany-> Articles 

## Model Properties:

#### 1. Fillable: 
title, category_id, writer_id, body, published_at, is_live, is_header, is_archived, cover_photo, cover_caption, cover_artist_id, thumbnail_same_as_cover, thumbnail, thumbnail_caption, thumbnail_artist_id, archived_at, add_to_ticker, ticker_expires_at

#### 2. Casts
- 'published_at' => 'datetime'
- 'is_live' => 'boolean'
- 'is_header' => 'boolean'
- 'is_archived' => 'boolean'
- 'thumbnail_same_as_cover' => 'boolean'
- 'add_to_ticker' => 'boolean'


## Observer:
1. ArticleObserver
   - Handles automatic creation and updating of article slug 

### Prerequisite:
- Register in the app\Providers\AppServiceProvider boot function


## Migrations
This contains these properties: 

- $table->id()
- $table->string('title')
- $table->string('slug')->unique()
- $table->foreignIdFor(User::class, 'writer_id')
                ->constrained('users')
- $table->foreignIdFor(ArticleCategory::class, 'category_id')
                ->constrained('article_categories')
                ->onDelete('cascade')
- $table->longText('body')
- $table->timestampTz('published_at')
- $table->boolean('is_live')->default(false)
- $table->boolean('is_header')->default(false) // only for live news
- $table->foreignIdFor(Article::class, 'series_id') // only for live news
                ->nullable()
                ->constrained('articles')
                ->onDelete('set null')
- $table->boolean('is_archived')->default(false)
- $table->string('cover_photo') // filename
- $table->text('cover_caption')->nullable()
- $table->foreignIdFor(User::class, 'cover_artist_id')
                ->constrained('users')
- $table->boolean('thumbnail_same_as_cover')->default(false)
- $table->string('thumbnail')->nullable() // filename
- $table->foreignIdFor(User::class, 'thumbnail_artist_id')
                ->constrained('users')
- $table->timestampTz('archived_at')->nullable()
- $table->boolean('add_to_ticker')->default(false)
- $table->timestamp('ticker_expires_at')->nullable()
- $table->timestamps()
- $table->softDeletes() // soft delete feature deleted_at column

## Resource
The data that will be returned:

- 'id' => $this->id
- 'title' => $this->title
- 'slug' => $this->slug,
- 'writer' => $this->writer->full_name
- 'category' => $this->category_id
- 'body' => $this->body
- 'published_at' => $this->published_at
- 'cover_photo' => $this->cover_photo
- 'cover_caption' => $this->cover_caption
- 'cover_artist' => $this->coverArtist->full_name ?? null
- 'thumbnail_same_as_cover' => $this->thumbnail_same_as_cover
- 'thumbnail' => $this->thumbnail
- 'thumbnail_artist' => $this->thumbnailArtist->full_name ?? null







