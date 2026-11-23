# Blog Platform

Milestones delivered:

- Milestone 1: Fully styled static SPA frontend (tabs, modals, mock data).
- Milestone 2: MySQL schema and PHP PDO DAO layer with test script.
- Milestone 3: FlightPHP CRUD API, Swagger, and Open Library related reading.

## Tech Stack

- Frontend: Bootstrap 5, vanilla JS, hash-based router
- Styling: Custom CSS theme (glassmorphism, gradients, dark theme)
- Icons: Bootstrap Icons
- Fonts: Google Fonts (`Inter`, `Playfair Display`)
- Data (M1): In-memory mock data (`frontend/js/mockData.js`)
- Backend (M2): PHP 8 + PDO (DAOs), MySQL 8, env-based config

## Project Structure

```
blog-platform/
  backend/
    .env.example
    .env                         # (not committed, create and copy .env.example)
    bootstrap.php                # loads .env
    database.sql                 # schema for all entities
    routes/
    services/
    dao/
      BaseDao.php
      UsersDao.php
      PostsDao.php
      CategoriesDao.php
      TagsDao.php
      CommentsDao.php
      PostTagsDao.php
    test_dao.php                 # CLI verification for DAOs
  frontend/
    index.html
    css/
      styles.css
    js/
      utils.js
      mockData.js
      modal.js
      navbar.js
      views.js
      router.js
      app.js
    assets/
  docs/
    ERD.png
```

## Features (Milestone 1)

- **SPA routing** via hash: instant navigation without page reloads.
- **Home** with hero, featured CTA, and post cards.
- **Post Detail** with category banner, metadata, tags, and comments list.
- **Auth pages** (`/login`, `/register`) with styled forms (static, non-functional yet).
- **Profile** with consistent layout and fields (static, non-functional yet).
- **Admin Dashboard** (`/dashboard`) with tabs for:
  - Posts, Categories, Tags, Users, Comments
  - Data tables with actions
  - Reusable modals for CRUD operations (in-memory)
- **Consistent design system**: typography, colors, chips, buttons, glass surfaces, tables, forms.

## Routes

- `/` — Home
- `/login` — Login
- `/register` — Register
- `/post/:id` — Post detail
- `/dashboard` — Admin dashboard
- `/profile` — Profile

## Admin Dashboard and CRUD Modals

Admin is implemented as a tabbed interface under `/dashboard`.

- **Posts**: create, edit, delete
- **Categories**: add, rename, delete
- **Tags**: add, rename, delete
- **Users**: invite, change role, disable
- **Comments**: approve, hide, delete

All actions open a Bootstrap modal (`frontend/js/modal.js`). Changes are applied to `window.MockData` arrays and the view re-renders via `Router.render()`. This is front-end only; persistence arrives in later milestones.

## Milestone 2 — Backend (Schema + DAOs)

### Database Schema (MySQL)

Entities (with constraints and indexes):

- `users` (id, name, email UNIQUE, password_hash, role [admin|user], created_at)
- `categories` (id, name UNIQUE, created_at)
- `posts` (id, author_id FK users, title, content, category_id FK categories, created_at)
- `tags` (id, name UNIQUE, created_at)
- `comments` (id, post_id FK posts CASCADE, user_id FK users SET NULL, content, status [visible|hidden], created_at)
- `post_tags` (post_id, tag_id) PK(post_id, tag_id), FKs CASCADE

Import the schema via `backend/database.sql` using phpMyAdmin or CLI:

- phpMyAdmin → Import → select `backend/database.sql`
- or CLI: `mysql -u root -p < backend\database.sql`

### DAO Layer (PHP PDO)

- `backend/dao/BaseDao.php` provides:
  - Shared PDO connection via `.env` (host, port, db, user, pass).
  - Helpers: `query/fetch/fetchAll/execute/lastInsertId`, transactions, pagination.
  - Dynamic builders: `buildInsert` (columns backticked), `buildUpdate`.
- Entity DAOs implement CRUD and specific queries:
  - `UsersDao`: `create/getById/getByEmail/listAll/update/delete`
  - `PostsDao`: `create/getById(list joins)/listAll(joins)/update/delete`
  - `CategoriesDao`: `create/getById/getByName/listAll/update/delete`
  - `TagsDao`: `create/getById/getByName/listAll/update/delete`
  - `CommentsDao`: `create/getById(joins)/listAll(joins)/listByPost/setStatus/update/delete`
  - `PostTagsDao`: `add/remove/getTagsForPost/getPostsForTag/replaceTagsForPost`

### Configure `.env`

Copy `.env.example` to `.env` and adjust:

- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_NAME=blog_platform`
- `DB_USER=root`
- `DB_PASS=` (set if your MySQL has a password)
- `DB_CHARSET=utf8mb4`

### Verify DAOs via CLI

Run the test script (PowerShell from project root):

```
php backend\test_dao.php
```

## Milestone 3 ? REST API and Swagger

The FlightPHP API provides CRUD for users, categories, tags, posts, and comments.
Post tags are managed through `GET /posts/{id}/tags` and `PUT /posts/{id}/tags`.
The API also supports comment moderation and lookup by email or name.
Write requests use JSON. User creation takes a `password` and hashes it on the server.

### Run locally

1. Start MySQL and follow the schema and `.env` setup above. Import the seed only
   into a fresh database.
2. Use PHP 8 with PDO MySQL, mbstring, and cURL enabled in Apache’s PHP configuration.
   Run `composer install` inside
   `backend` to install FlightPHP and swagger-php.
3. Serve the project with Apache. Place it in `htdocs` or map an Apache Alias to
   the project directory. Enable `mod_rewrite` and allow `.htaccess` overrides
   with `AllowOverride All` for the project directory. Start Apache.
4. If your local site is mounted at `/blogplatform`, open
   [Swagger UI](http://localhost/blogplatform/backend/public/v1/docs/).
   The [OpenAPI JSON](http://localhost/blogplatform/backend/public/v1/docs/swagger.php)
   is available from the same directory. Substitute your local mount path as needed.

### Related reading

`GET /related-reading?q=PHP+programming` returns up to five books with titles,
authors, and links for a given blog topic. The service uses the
[Open Library Search API](https://openlibrary.org/dev/docs/api/search), which
requires no API key for this request. Requests time out after ten seconds.
Provider failures return a friendly HTTP `503` and are written to
`backend/logs/error.log`. Give the web server write access to that directory
or permission to create it under `backend`. The related-reading UI is part
of the Milestone 4 frontend integration.

The seeded admin is `amina@example.com` (`BlogAdmin2026!`). A seeded regular
user is `marko@example.com` (`BlogDemo2026!`). These are demonstration accounts
for the sample database. Authentication and protected routes arrive in Milestone 4.
The Milestone 3 API is intended for local use.

## Running Frontend Locally

Open `frontend/index.html` directly in a browser.

## ERD Draft

See `docs/ERD.png` for the Entity-Relationship Diagram.