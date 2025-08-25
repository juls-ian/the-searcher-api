# Article 

Documentation shall consist Article model's properties, resource and migrations

## Relationships 
1. multimediaArtists <-belongsToMany-> User
   1. *since this is m:m it has a pivot table named 'multimedia_user'*
2. thumbnailArtist <-belongsTo-> User  
3. publisher <-belongsTo-> User  

## Model Properties:

#### 1. Fillable: 
 'title', 'category', 'caption', 'published_at', 'files', 'thumbnail' 'thumbnail_artist_id'

#### 2. Casts
- 'files' => 'array',
- 'published_at' => 'datetime'

#### 3. Attributes 
- 'thumbnail_credit_type' => 'photo'

#### 4. booted()
- sets the publisher_id automatically

## Observer:
1. MultimediaObserver
   - Handles automatic creation and updating of multimedia slug 

### Prerequisite:
- Register in the app\Providers\AppServiceProvider boot function


## Migrations: Multimedia
This contains these properties:
- $table->id();
- $table->string('title');
- $table->string('slug')->unique();
- $table->enum('category', ['gallery', 'video', 'illustration', 'segment']);
- $table->string('caption');
- $table->timestampTz('published_at');
- $table->string('files');
- $table->string('thumbnail');
- $table->foreignIdFor(User::class, 'thumbnail_artist_id')
-     ->constrained('users');
- $table->timestamps();
- $table->softDeletes();

## Migrations: Pivot Table 
This contains these properties: 

- $table->id();
- $table->foreignId('multimedia_id')
    ->constrained()
    ->onDelete('cascade');
- $table->foreignId('user_id')
    ->constrained()
    ->onDelete('cascade');
- $table->timestamps();
- $table->unique(['multimedia_id', 'user_id']);

## Resource
The data that will be returned:

- 'id' => $this->id,
- 'title' => $this->title,
- 'slug' => $this->slug,
- 'category' => $this->category,
- 'caption' => $this->caption,
- 'published_at' => $this->published_at,
- 'files' => $this->files,
- 'multimedia_artists' => $this->multimediaArtists->pluck('full_name'), # array of names
- 'thumbnail' => $this->thumbnail,
- 'thumbnail_artist' => $this->thumbnailArtist->full_name







