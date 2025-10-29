# Initial Setup 

The database will be **Postgres**

Initiate **php artisan install:api**

## Foundation 

1. php artisan make:model -a 
    1. User (automatically generated)
    2. Article Category
    3. Article 
    4. create a function in the Staff model to get the full name 
2. configure the migrations
    1. Staff, Article, Article Category
    - foreignKeys should not be fillables
    - slugs should not be fillable
    - full_name & staff_id should not be fillable
3. configure Article, User, Article Category factories  
4. add relationships between Article, User, Article Category
5. create staff_id algorithm in the User model 
6. run migrations and seed db 
7. set 'timezone' => 'ASIA/MANILA',  in the config/app.php
   
## Article 

1. make Article Resource
2. declare Article controller route 
3.  configure Article model
    1.  create store and update article request
    2. create observer for the slug generation 
        1. register the observer in app/Providers/AppServiceProvider.php in the boot method
    3. config the publish_at date and time generation logic
    4. prepare the config for the image upload
    5. handle image upload 
    6. code CRUD of the articles, also implement soft delete

## User 

1. make a User Controller
2. make User Resource
3. declare the user route
4. create observer for slugs generation
   1. register observer in app/Providers/AppServiceProvider.php in the boot method

### Authentication 

1. make Auth Controller 
2. declare Auth and User controller route
3. handle the authentication for the user 
    1.  setup 
        1. php artisan config:publish cors
        2. config SESSION_DOMAIN .env 
    2. login
        1. implement token generation 
        2. set token expiration
        3. middleware that handles expired tokens gracefully 
            - create HandleExpiredToken middle (php artisan make:middleware HandleExpiredTokens)
            - register in the bootstrap in the bootstrap/app.php 
            - register in api route
            - set an expiration (default) in the config\sanctum, the standard is 60 minutes (only used for          cookie-based auth)  [optional] [SPA authentication with Sanctum's CSRF/cookie mechanism]
            - automate pruning expired tokens in console.php
    3. logout

### Account Recovery 

 1.  handle forgot password & reset password
     1.  ensure User model has **Notifiable** and **CanResetPassword** trait
     2.  create Forgot Password & Reset Password Controllers (invokable or not)
     4.  declare controllers in api route
          - add rate limiter     
     5.  handle their respective logics 
     6.  test the endpoint 
         1.  without email: 
            - MAIL_MAILER=log (.env)
            - php artisan queue:work
  
         2. with email: 
            - pick an email tester like mailtrap
            - create a mailbox in mailtrap 
            - setup the .env
            - set dummy reset-password route in web route to avoid password.reset route error
            - php artisan make:notification ResetPasswordNotification (optional) [to override the default mail notification with out  own version that points to your frontend or app's reset page]
              - register custom notification to User model to

### Registration 
*can only be done by admin* 

1. create requests for:
   1. Store User Request
   2. Update User Request
   
2. php artisan make:policy UserPolicy --model=User
3. composer require propaganistas/laravel-phone
4. make the password in the user migration nullable
5. register User Controller to api routes
6. configure the store and update user request
7. make a Set Password Controller (invokable)
8. register the controller in api route  
9.  php artisan make:notification SetPasswordNotification
10. configure the custom notification
11. handle registering user in the User Controller made earlier

### Email Verification

1.  User model must implement MustVerifyEmail
2.  handle email verification in Set Password Controller
3. make a Email Verification Controller and handle here the ff: (refactored)
   1. email verification notice 
   2. email verification handler
   3. resending email verification
4. set the routes for these three

### User Management 
*done by admin (total control)*, 
*can also be by the user itself (limited)*

1. handle in the User Controller the ff:
   1. showing user
   2. updating user
   3. deleting user & soft delete 

## Article Category 

1. make article category resource 
2. handle crud in the article category controller
3. implement soft delete
4. handle slug generation in the observer 
   1. register the observer
5. declare in api routes

## Authorization 

1. implement Article Policies 
2. implement custom error handling or global exception handler 
   1. if global exception handler do it in the bootstrap/app.php
3. implement User Policies
4. implement Article Category Policies


## Community Segments
*this shall have other two dependent tables: segments_poll and segment_article* 

1. make these components:
   - migration
   - controller
   - model 
   - resource 
   - policy 
   - request
   - factory
   - observer
2. declare the api route 
3. handle the relations to Segments Poll/Article and User 
4. handle slug generation in the observer
5. register observer in app/Providers/AppServiceProvider.php in the boot method
6. implement crud

### Segments Poll & Segments Article
1. the components shall be:
   - migrations
   - models
   - resource
   - factory
   - policies
2. handle the relationship to Community Segments

## Multimedia 
1. make these components:
   - model 
   - migration 
   - resource 
   - policy 
   - request 
   - factory 
   - observer
2. declare it in the api route 
3. handle the relations to User  
4. handle slug generation in the observer
5. register observer in app/Providers/AppServiceProvider.php in the boot method
6. implement crud
7. create a pivot table for the m:m relationship between User and Multimedia 

## Editorial Board 
1. make these components:
   - model 
   - migration 
   - resources 
2. update the UserController's store method to add term/ed board to the editorial_board table 
3. create three additional methods to manage the term of the user 
4. define the relationship its relationship to the User model 
5. implement a feature to get the current_term and all_term of the user 
6. declare in the api route the three additional methods

## Issue 
1. make these components of the Issue:
   - model 
   - migration 
   - resources
   - policies 
   - requests
2. implement crud features to manage issues 
3. implement slug generation and register it in the AppServiceProvider
4. define in the api routes

## Bulletin 
1. make these components of the Bulletin:
   - model 
   - migration 
   - resources
   - policies
   - request
2. define the relationship on the Bulletin and User model 
3. implement crud features to manage bulletins 
4. implement slug generation and register it in the AppServiceProvider
5. define in the api routes

## Archives 
1. make these components of the Archive:
   - model 
   - migration 
2. implement polymorphic relationship 
   - php artisan make:trait Archivable
3. add archive functions in each of the models as well in the api route
4. implement crud features to manage archives 
5. implement soft and hard deletion
6. impletion restoration
7. define in the api of this controller
8. enforceMorphMap in the AppServiceProvider 

### Archiving Models 
1. make a trait 
2. use the trait on the respective models 
3. implement own archiving on each models (separate from the archive model)
4. Bulletin, Article, Community Segment, Multimedia, and Issue shall be archivable 

## Soft and Hard Deletion and Restore
1. implement this feat on the Article, User, Multimedia, Community Segments, Bulletin, Issue, and Archive
2. declare route binding of each models through the AppServiceProvider if using a route model binding in the crud functions of the controllers

## Calendar
1. make these components of the Calendar:
   - model 
   - migration 
   - resources
   - policies
   - request
2. define the relationship on the Calendar and User model 
3. implement crud features to manage calendar 
4. implement slug generation and register it in the AppServiceProvider
5. define in the api routes

## BoardPosition 
a last minute addition 

1. php artisan make:model BoardPosition -a
2. work the relationship between the User and BoardPosition 
3. revise the existing user's 'board_position' column 
4. add migration to add board position id foreign key into the user table 
5. make a BoardPositionSeeder 
6. use it in the DatabaseSeeder and assign each user a board_position


## Search Feature

### Dependencies Overview 
1. because of the multiple models we shall use:
   1. Laravel Scout 
   2. Algolia*
   3. Meilisearch*  
   4. Postgres Full Text Search +  Postgres Scout driver (no extra service)

### Dependency Installation
1. composer require laravel/scout
2. php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"

#### Dependency Installation: Laravel Scout Postgres
1. composer require pmatseykanets/laravel-scout-postgres
2. in config/scout.php 
3. 'driver' => 'pgsql',

#### Dependency Installation: Meilisearch 
1. composer require laravel/scout meilisearch/meilisearch-php http-interop/http-factory-guzzle
2. in config/scout.php
   1. 'driver' => 'meilisearch',
3. in .env
   1. SCOUT_DRIVER=meilisearch
   2. MEILISEARCH_HOST=http://127.0.0.1:7700
   3. MEILISEARCH_KEY=MASTER_KEY
4. docker run -it --rm -p 7700:7700 getmeili/meilisearch

#### Dependency Installation: Algolia
1. composer require algolia/algoliasearch-client-php
2. php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
3. in .env
   1. SCOUT_DRIVER=algolia
   2. ALGOLIA_APP_ID=
   3. ALGOLIA_API_KEY= admin api key
4. in scout.php
   1. 
    'algolia' => [
        'id' => env('ALGOLIA_APP_ID', ''),
        'secret' => env('ALGOLIA_API_KEY', ''),
   2. 'queue' => env('SCOUT_QUEUE', true),
   3. 'driver' => env('SCOUT_DRIVER', 'algolia'),


##### Algolia Problem Fix
Fix for the *Impossible to connect, please check your Algolia Application Id* issue
1. php --ini
2. download Grab the latest CA bundle here (official from cURL):
🔗 https://curl.se/ca/cacert.pem
3. move to a folder 
4. in the php.ini write
   1. curl.cainfo = "file-location\cacert.pem"
   2. openssl.cafile = "file-location\cacert.pem"

### Implementation 
1. make the models Searchable
2. add the toSearchableArray in the model 
3. php artisan scout:import "App\Models\Article" *add all the searchable models*
4. add a **search service** which will handle the logic of building and running the search
5. add a **search controller** that handles the request or response
6. add the filterable and sortable attributes
7. php artisan scout:sync-index-settings
