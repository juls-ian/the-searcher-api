# Initial Setup 

The database will be **Postgres**

Initiate **php artisan install:api**

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
7. make resources for:
    1. Article
    2. User
8. declare Article controller route 
9. configure Article model
    1.  store and update article request
    2. create observer for the slug generation 
        1. register the observer in app/Providers/AppServiceProvider.php in the boot method
    3. config the publish_at date and time generation logic
    4. prepare the config for the image upload
    5. handle image upload 
    6. code CRUD of the articles
10. php artisan make:controller <>: 
    1. User Controller 
    2. Auth Controller
11. declare Auth and User controller route
12. handle the authentication for the user 
    1.  php artisan config:publish cors
    2. config SESSION_DOMAIN .env 
    3. login
        1. implement token generation 
        2. set token expiration
    4. logout