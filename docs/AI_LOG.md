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



