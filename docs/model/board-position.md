# BoardPosition 

Documentation shall consist BoardPosition model's properties, resource and migrations

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
'name', 'category'

## Migrations
This contains these properties: 

- $table->id();
- $table->string('name')->unique();
- $table->string('category');
- $table->timestamps();

## Resource
The data that will be returned:

- 'term' => $this->term,
- 'current' => $this->is_automatically_current || $this->is_current,
- 'archived' => $this->is_archived,
- 'member' => [
     'id' => $this->user->id,
     'full_name' => $this->user->full_name,
     'pen_name' => $this->user->pen_name,
     'board_position' => $this->user->board_position,
     'profile_pic' => $this->user->profile_pic,
     'status' => $this->user->status,
     'role' => $this->user->role,
]






