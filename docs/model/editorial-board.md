# Article 

Documentation shall consist Article model's properties, resource and migrations

## Relationships 
1. user <-belongsTo-> User 

## Model Properties:

#### 1. Fillable: 
'user_id', 'term', 'is_current'

## Migrations
This contains these properties: 

- $table->id();
- $table->foreignId('user_id')
-     ->constrained()
-     ->onDelete('cascade');
- $table->string('term');
- $table->boolean('is_current')->default('false');
- $table->timestamps();
- 








