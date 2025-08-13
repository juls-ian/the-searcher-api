# Bulletin 

Documentation shall consist Bulletin model's properties, resource and migrations

## Relationships 
1. writer <-belongsTo-> User 
2. coverArtist <-belongsTo-> User  

## Model Properties:

#### 1. Fillable: 
'title', 'category', 'writer_id', 'details', 'published_at', 'cover_photo', 'cover_artist_id'

#### 2. Casts
- 'published_at' => 'datetime',
- 'cover_photo' => 'string'


## Observer:
1. BulletinObserver
   - Handles automatic creation and updating of bulletin slug 

### Prerequisite:
- Register in the app\Providers\AppServiceProvider boot function


## Migrations
This contains these properties: 

- $table->id();
- $table->string('title');
- $table->string('slug')->unique();
- $table->enum('category', ['advisory', 'announcement']);
- $table->foreignIdFor(User::class, 'writer_id')
-     ->constrained('users');
- $table->longText('details');
- $table->timestampTz('published_at');
- $table->string('cover_photo');
- $table->foreignIdFor(User::class, 'cover_artist_id')
-     ->constrained('users');
- $table->timestamps();

## Resource
The data that will be returned:

- 'id' => $this->id,
- 'title' => $this->title,
- 'slug' => $this->slug,
- 'category' => $this->category,
- 'writer' => $this->writer->full_name ?? null,
- 'details' => $this->details,
- 'published_at' => $this->published_at,
- 'cover_photo' => $this->cover_photo,
- 'cover_artist' => $this->coverArtist->full_name ?? null







