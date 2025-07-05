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