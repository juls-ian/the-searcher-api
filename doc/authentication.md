# Authentication

## Guide in handling the user authentication 

### Prerequisite:
1. php artisan config:publish cors
2. User model
   - set HasApiTokens
3. config: .env
   - SESSION_DOMAIN=localhost 
4. config: cors.php
   - support_credentials => true
5. config: postman
   - script: 
    - pm.sendRequest({
        url: 'http://localhost:8000/sanctum/csrf-cookie',
        method: "GET"
        },function(err, res, {cookies}) {
         if(!err) {
            pm.globals.set('csrf-token', cookies.get('XSRF-TOKEN'))
            }
        })
6. make UserController 
7. make LoginRequest

### Authentication shall consist these features:
1. Login
   - LoginRequest
   - implement API token auth (Sanctum)
     - set token expiration 
     - set token pruning
   - rate limiter
   - token refresh 
   - remember me 
2. Logout 
3. Reset Password 
4. Forgot Password
5. Get current authenticated user*

### API Tokens:
Sanctum will be used in token auth.   
There shall be two types of tokens 
  1. **auth token** = normal tokens
    - expires in 1 hr
  2. **remember tokens** = remember me 
    - expires in 7 days

### Pruning Tokens
Expired tokens shall be pruned in order to keep the database clean 

#### Prerequisite:
1. php artisan make:middleware HandleExpiredTokens
   - this middleware shall handle the expired tokens gracefully 
2. register the middleware in bootstrap/app.php
3. register the middleware in the api route
4. automate token pruning in routes/console.php 


