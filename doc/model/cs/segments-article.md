# Segments Article

Documentation shall consist Segments Article model's properties, resource and migrations

## Relationships 
1. segment: belongsTo -> CommunitySegment 


## Model Properties:

#### 1. Fillable: 
'segment_id', 'body'

## Migrations
This contains these properties: 

- $table->id();
- $table->foreignIdFor(CommunitySegment::class, 'segment_id')
    ->constrained('community_segments')
    ->onDelete('cascade');
- $table->longText('body');
- $table->timestamps();
- $table->softDeletes();

## Resource
The data that will be returned:

- 'id' => $this->id,
- 'segment_id' => $this->segment_id,
- 'body' => $this->body







