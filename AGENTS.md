# Blog Platform: guidance for coding assistants

## Project and current scope

This is a student blog platform for IT 2001 Web Programming. Current work covers
Milestones 1 and 2: a static SPA and a MySQL/PDO DAO layer. Frontend data is still
mocked; backend authentication is not implemented. See README.md for features
and setup instructions.

Implement the milestone or change requested by the user. Do not assume that later
milestone features are already available or add them without a relevant request.

## Technology stack

- Frontend: HTML, CSS, vanilla JavaScript, Bootstrap 5, and Bootstrap Icons.
- Navigation: hash-based SPA routing without full page reloads.
- Backend: PHP 8.0 or newer with PDO and the pdo_mysql extension.
- Database: MySQL 8, InnoDB tables, and utf8mb4 encoding.
- Configuration: backend/bootstrap.php loads backend/.env into environment variables.
- Planned REST framework: FlightPHP; it is not implemented in the current DAO layer.

## Folder structure

- frontend/: HTML entry point, css/ for styles, js/ for browser logic, and assets/.
- backend/dao/: database access; routes/ and services/ are reserved for later layers.
- backend/database.sql: schema and sample data; bootstrap.php: configuration loading;
  test_dao.php: DAO smoke test; .env.example: configuration template.
- docs/: ERD and the milestone-by-milestone AI usage record in AI_LOG.md.

## Architecture rules

- Keep frontend HTML, CSS, and JavaScript separate from PHP backend code.
- Keep SQL and database access inside the DAO layer. Reuse BaseDao connection,
  CRUD, query, pagination, and transaction helpers where appropriate.
- The five main entity DAOs inherit CRUD operations from BaseDao. PostTagsDao
  manages a composite-key relationship through its dedicated methods; do not use
  inherited methods that assume an id or created_at column on post_tags.
- When implementing later milestones, use the flow: frontend service -> FlightPHP
  route -> backend service -> DAO -> MySQL. Backend services own business rules
  and are the only application layer that calls DAOs. The CLI DAO test is a
  deliberate direct caller for verification.
- Keep mock frontend data distinct from persistent MySQL data. Replace mock data
  with AJAX-backed services when frontend integration is requested.
- Use reusable middleware for planned JWT checks, role checks, request logging,
  and error handling. Do not implement these checks independently in every route.

## Database and security rules

- Use PDO prepared statements and bound parameters for values. Validate or
  allowlist dynamic column names and sort expressions; SQL identifiers cannot be
  protected by value placeholders.
- Preserve existing tables, columns, constraints, and deletion rules unless the
  requested change requires modifying them.
- Posts reference users and categories; comments reference posts and optionally
  users; post_tags joins posts and tags. Insert parent records before children.
- Use PHP password_hash() and password_verify() for passwords. Never store real
  plaintext passwords or expose password hashes through public API responses.
- Store real credentials and future JWT secrets/API keys in ignored configuration.
  Commit templates with placeholders only. Do not print or commit local secrets.
- Sample accounts are demonstration data, not production credentials.
- The SQL seed uses explicit IDs for a fresh database. Do not reimport it into an
  occupied database, drop tables, or erase user data without explicit authorization.

## Naming and code style

- Follow the existing two-space indentation in PHP and JavaScript.
- Use PascalCase PHP class names and matching filenames, such as UsersDao.php.
- Use camelCase method/function names, such as getByEmail and listByPost.
- Use lowercase snake_case table and column names, such as password_hash.
- Preserve the current browser JavaScript IIFE and window export pattern unless
  the task explicitly calls for a module/build-system migration.
- Keep changes focused and readable. Avoid unrelated refactors or new dependencies.


## Documentation and milestone workflow

- Update README.md when setup, configuration, or user-visible behavior changes.
- Keep this file aligned with the actual architecture in each milestone.
- Record actual AI tools, relevant prompts, work performed, verification, and
  observed mistakes/fixes in docs/AI_LOG.md for each milestone. Never fabricate
  mistakes, test results, or student authorship. The student must review and
  understand AI-assisted documentation and code before submission.
- Course submissions use milestone1 through milestone5 branches and a pull request
  with ibu-web-programming2001 as reviewer/collaborator. Do not merge a milestone
  PR on the student's behalf; the course reviewer handles that merge.
