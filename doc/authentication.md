# Authentication

## Guide in handling the user authentication 

### Pre-requisite
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

### Authentication shall consist these features:

1. Login
   - LoginRequest
   - implement token generation
     - set token expiration 
   - rate limiter
   - token refresh 
   - remember me 
2. Logout 
3. Reset Password 
4. Forgot Password