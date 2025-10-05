# User 

Documentation shall consist User model's properties, resource and migrations

## Relationships 
The User contains the most relationships to other models

### Article 
1. writtenArticles <-hasMany-> Article 
2. articleCoverContributions <-hasMany-> Article 
3. articleThumbnailContributions <-hasMany-> Article

### CommunitySegment
4. writtenSegments <-hasMany-> CommunitySegment
5. segmentCoverContributions <-hasMany-> CommunitySegment

### Multimedia
6. multimediaContributions <-hasMany-> Multimedia
7. multimediaThumbnailContributions <-hasMany-> Multimedia

### EditorialBoard 
8. editorialBoards <-hasOne-> EditorialBoard

### Bulletin 
9. writtenBulletin <-hasMany-> Bulletin
10. bulletinCoverContributions <-hasMany -> Bulletin 

## Model Properties:

#### 1. Fillable: 
email, password, first_name, last_name, pen_name, year_level, course, phone, board_position, role, status, joined_at, profile_pic

#### 2. allContribution()
Getting all contributions of the user 

#### 3. getFullNameAttribute() 
Concatenates the first_name and last_name 

#### 4. generateStaffId() 
Generates the staff_id of the user upon user creation 

#### 5. boot() 
- auto-generate the staff_id when creating user 

#### 6. currentTerm()
- retrieves the current term of the user
  
#### 7. getCurrentTermAttribute(), getAllTerms(), getAllTermsCollection()
- alternative methods to get all the terms 
  

## Migrations
This contains these properties: 

- $table->id();
- $table->string('first_name')
- $table->string('last_name')
- $table->string('full_name', 200)->storedAs("first_name || ' ' || last_name")->nullable()
- $table->string('full_name_slug')->unique();
- $table->string('pen_name')
- $table->string('pen_name_slug')->unique();
- $table->string('staff_id', 100)->unique()
- $table->string('email')->unique()
- $table->string('year_level')
- $table->string('course')
- $table->string('phone')
- $table->string('board_position')
- $table->enum('role', ['admin', 'editor', 'staff'])->default('staff')
- $table->enum('status', ['active', 'inactive', 'alumni'])->default('active')
- $table->date('joined_at')
- $table->date('left_at')->nullable()
- $table->string('profile_pic')
- $table->timestamp('email_verified_at')->nullable()
- $table->string('password')
- $table->rememberToken()
- $table->timestamps()

## Resource
The data that will be returned:

- 'id' => $this->id,
- 'full_name' => $this->full_name,
- 'fn_slug' => $this->full_name_slug,
- 'pen_name' => $this->pen_name,
- 'staff_id' => $this->staff_id,
- 'email' => $this->email,
- 'board_position' => $this->board_position,
- 'year' => $this->year_level,
- 'course' => $this->course,
- 'phone' => $this->phone,
- 'role' => $this->role,
- 'current_term' => $this->currentTerm(),
- // editorialBoards relation must be loaded first in t- controller
- 'all_terms' => $this->whenLoaded('editorialBoards- function () {
-     return $this->editorialBoards->pluck('term');
- }, []),
- 'status' => $this->status,
- 'joined_at' => $this->joined_at,
- 'left_at' => $this->left_at,
- 'profile_pic' => $this->profile_pic,







