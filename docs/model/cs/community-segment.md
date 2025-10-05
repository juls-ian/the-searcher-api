# Community Segment 

Documentation shall consist Community Segment model's properties, resource and migrations

## Relationships 
1. writer: belongsTo -> User 
2. series: hasMany -> CommunitySegment
3. coverArtist: belongsTo  -> User
4. segmentArticles: hasOne -> SegmentsArticle 
5. segmentPolls: hasOne -> SegmentsPoll

## Model Properties:

#### 1. Fillable: 
'title', 'segment_type', 'writer_id', 'published_at', 'series_of', 'series_order', 'segment_cover', 'cover_artist_id', 'cover_caption'

#### 2. Casts:
- 'published_at' => 'datetime'

#### 3. booted()
*booted() is called after observers/events registered*
- soft deletes the children segment when parent is deleted 
- restores the children segment when parent is restored

## Migrations
This contains these properties: 

- $table->id()
- $table->string('title')
- $table->string('slug')->unique()
- $table->enum('segment_type', ['article', 'poll'])
- $table->foreignIdFor(User::class, 'writer_id')
    ->constrained('users')
- $table->foreignIdFor(CommunitySegment::class, 'series_of')
    ->nullable()
    ->constrained('community_segments')
    ->onDelete('set null')
- $table->dateTimeTz('published_at')
- $table->integer('series_order')->nullable()
- $table->string('segment_cover')
- $table->foreignIdFor(User::class, 'cover_artist_id')
- $table->string('cover_caption')
- $table->timestamps()
- $table->softDeletes()

## Resource
The data that will be returned:

- 'id' => $this->id,
- 'title' => $this->title,
- 'segment_type' => $this->segment_type,
- 'writer' => $this->writer->fullname,
- 'series_of' => $this->series_of,
- 'published_at' => $this->published_at,
- 'series_order' => $this->series_order,
- 'segment_cover' => $this->segment_cover,
- 'cover_artist_id' => $this->coverArtist->fullname,
- 'cover_caption' => $this->cover_caption,
- 'poll_segments' => $this->when($this->segmentPolls, n- SegmentPollResource($this->segmentPolls)),
- 'article_segments' => $this->when($this->segmentArticle- new SegmentArticleResource($this->segmentArticles)),







