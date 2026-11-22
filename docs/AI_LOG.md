# AI Usage Log

## Milestone 2 — Database and DAO Layer

### Tool and purpose

Used ChatGPT/Codex to add realistic sample data to `backend/database.sql` and create missing project folder placeholders.

### Prompt

> Add realistic sample data to `backend/database.sql` using `INSERT INTO` statements at the end of the file. Populate all tables (`users`, `categories`, `tags`, `posts`, `comments`, `post_tags`) in the correct order to respect foreign key relationships. Ensure all required fields are populated, IDs are correctly referenced, and user passwords are hashed using PHP-compatible password hashes. Keep the existing database schema unchanged.


### Additional prompt: project folder placeholders

```text
Create the missing project folders: backend/middleware, backend/config,
backend/logs, and frontend/views.

Add an empty .gitkeep file to middleware, config, and views so Git can
track them. Keep backend/logs excluded from Git using the existing
.gitignore rules.

Do not modify existing code, move files, or change the current .env
configuration. These folders are placeholders for later milestones.
```




## Milestone 3 

### Prompt


> Adapt the existing FlightPHP CRUD routes to this year's blog database. Return HTTP 201 when a record is created and 404 when it is missing. Hash passwords on the server and exclude password hashes from user responses.

> Add a related-reading endpoint that searches Open Library by blog topic and returns book titles, authors, and links. Set a timeout, log provider failures, return a friendly error, and document the endpoint in Swagger.

### Issues observed and fixed

1. The first validation edit generated invalid PHP in the posts and comments
   field maps. PHP lint found the bad entries. Codex corrected them and reran
   lint successfully.
2. The first HTTP CRUD test found that FlightPHP passes path IDs as strings.
   The inherited integer-typed callbacks failed before reaching the service.
   Codex updated the callbacks to accept strings and cast the matched IDs,
   then reran CRUD checks successfully.

