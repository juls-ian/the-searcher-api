# Authorization 

## Guide in handling all the authorization in the models 

### Hierarchy and Roles
The authorization hierarchy of the api is:

1. **Admin** 
   - handles staff management 
   - has all the abilities of editor and staff
2. **Editor**
   - manages all the publication related content
   - has all the abilities of staff
3. **Staff**
   - view dashboard or analytics 
   - view published content 
   - update own profile or information
   - view other staff's basic information 


### Article Policy

1. handles the article policies 
2. handle in the bootstrap/app the authorization exceptions for custom exceptions

### User Policy 
1. handle the user management authorization
2. only admin can perform this

