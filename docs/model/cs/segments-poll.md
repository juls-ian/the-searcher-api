# Segments Poll 

Documentation shall consist Segments Poll model's properties, resource and migrations

## Relationships 
1. segment: belongsTo -> CommunitySegment 


## Model Properties:

#### 1. Fillable: 
'segment_id', 'question', 'options', 'ends_at'

#### 2. Casts:
- 'options' => 'array',
- 'ends_at' => 'datetime'

## Migrations
This contains these properties: 

- $table->id();
- $table->foreignIdFor(CommunitySegment::class, 'segment_id')
    ->constrained('community_segments')
    ->onDelete('cascade');
- $table->text('question');
- $table->text('options');
- $table->dateTime('ends_at');
- $table->timestamps();
- $table->softDeletes();

## Resource
The data that will be returned:

- 'id' => $this->id,
- 'segment_id' => $this->segment_id,
- 'question' => $this->question,
- 'options' => $this->options,
- 'ends_at' => $this->ends_at







