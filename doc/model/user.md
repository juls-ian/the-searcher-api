# User 

Documentation shall consist User model's properties, resource and migrations

## Relationships 
1. writtenArticles <-hasMany-> Article 
2. coverContributions -> Article 
3. thumbnailContributions -> Article

## Model Properties:

#### 1. Fillable: 
email, password, first_name, last_name, pen_name, year_level, course, phone, board_position, role, term, status, joined_at, profile_pic

#### 2. allContribution()
Getting all contributions of the user 

#### 3. getFullNameAttribute() 
Concatenates the first_name and last_name 

#### 4. generateStaffId() 
Generates the staff_id of the user upon user creation 

#### 5. boot() 
- auto-generate the staff_id when creating user 

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
- $table->string('term')
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

- 'id' => $this->full_name
- 'pen_name' => $this->pen_name
- 'staff_id' => $this->staff_id
- 'email' => $this->email
- 'board_position' => $this->board_position
- 'year' => $this->year_level
- 'course' => $this->course
- 'phone' => $this->phone
- 'role' => $this->role
- 'term' => $this->term
- 'status' => $this->status
- 'joined_at' => $this->joined_at
- 'left_at' => $this->left_at
- 'profile_pic' => $this->profile_pic







