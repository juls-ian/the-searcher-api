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

1. implement the article policies 
2. handle in the bootstrap/app the authorization exceptions for custom exceptions

### User Policy 
1. implement the user management authorization
2. only admin can perform this


### Article Category Policy 
1. implement policies
2. only the admin and editor can perform this

### Community Segment Policy 
1. implement the policies 
2. only admin and editor can manage segments