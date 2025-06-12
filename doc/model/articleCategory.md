# Article Category 

Documentation shall consist Article model's properties, migrations, and factory?

## Relationships 
1. articles <-belongsToMany-> Article 


## Model Properties:

#### 1. Fillable: 
name

#### 2. Casts
- 'published_at' => 'datetime'
- 'is_live' => 'boolean'
- 'is_header' => 'boolean'
- 'is_archived' => 'boolean'
- 'thumbnail_same_as_cover' => 'boolean'
- 'add_to_ticker' => 'boolean'


## Observer 
Handles automatic creation and updating of article slug 

### Prerequisite:
- Register observer in the app\Providers\AppServiceProvider boot function
  - creating()
  - updating()


## Migrations
This contains these properties: 

- $table->id();
- $table->string('name');
- $table->string('slug')->unique();
- $table->foreignIdFor(ArticleCategory::class, 'parent_id') // define FK parent_id
    ->nullable()  // because onDelete('set null') implies the column can be null
    ->constrained('article_categories')
    ->onDelete('set null'); // set parent_id to NULL if the parent category is deleted
- $table->timestamps();

## Resource
The data that will be returned:

*no data yet*






