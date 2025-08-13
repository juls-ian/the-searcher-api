# Issue 

Documentation shall consist Issue model's properties, resource and migrations

## Relationships 
1. none for now

## Model Properties:

#### 1. Fillable: 
'title', 'slug', 'description', 'published_at', 'editors', 'writers', 'photojournalists', 'artists', 'layout_artists', 'contributors', 'issue_file', 'thumbnail',

#### 2. Casts
- 'editors' => 'array',
- 'writers' => 'array',
- 'photojournalists' => 'array',
- 'artists' => 'array',
- 'layout_artists' => 'array',
- 'contributors' => 'array',
- 'published_at' => 'datetime'


## Observer:
1. IssueObserver
   - Handles automatic creation and updating of issue slug 

### Prerequisite:
- Register in the app\Providers\AppServiceProvider boot function


## Migrations
This contains these properties: 

- $table->id();
- $table->string('title');
- $table->string('slug')->unique();
- $table->longText('description');
- $table->timestampTz('published_at');
- $table->json('editors');
- $table->json('writers');
- $table->json('photojournalists');
- $table->json('artists');
- $table->json('layout_artists');
- $table->json('contributors');
- $table->string('issue_file');
- $table->string('thumbnail');
- $table->timestamps();

## Resource
The data that will be returned:

- 'id' => $this->id,
- 'title' => $this->title,
- 'slug' => $this->slug,
- 'description' => $this->description,
- 'published_at' => $this->published_at,
- 'editors' => $this->editors,
- 'writers' => $this->writers,
- 'photojournalists' => $this->photojournalists,
- 'artists' => $this->artists,
- 'layout_artists' => $this->layout_artists,
- 'contributors' => $this->contributors,
- 'file' => $this->issue_file,
- 'thumbnail' => $this->thumbnail,







