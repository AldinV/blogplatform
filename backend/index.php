<?php
require_once __DIR__ . '/bootstrap.php';
require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/routes/api_helpers.php';

Flight::map('notFound', function () {
  Flight::json(['error' => 'Endpoint not found'], 404);
});
Flight::map('error', function (Throwable $error) {
  apiError($error);
  Flight::response()->send();
});

// CORS
Flight::before('start', function () {
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type, Authorization');
  if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204); exit;
  }
});

// Services
require_once __DIR__ . '/services/BaseService.php';
require_once __DIR__ . '/services/UsersService.php';
require_once __DIR__ . '/services/CategoriesService.php';
require_once __DIR__ . '/services/TagsService.php';
require_once __DIR__ . '/services/PostsService.php';
require_once __DIR__ . '/services/CommentsService.php';
require_once __DIR__ . '/services/ReadingService.php';

Flight::register('usersService', 'UsersService');
Flight::register('categoriesService', 'CategoriesService');
Flight::register('tagsService', 'TagsService');
Flight::register('postsService', 'PostsService');
Flight::register('commentsService', 'CommentsService');
Flight::register('readingService', 'ReadingService');

// Routes
require_once __DIR__ . '/routes/UsersRoutes.php';
require_once __DIR__ . '/routes/CategoriesRoutes.php';
require_once __DIR__ . '/routes/TagsRoutes.php';
require_once __DIR__ . '/routes/PostsRoutes.php';
require_once __DIR__ . '/routes/CommentsRoutes.php';
require_once __DIR__ . '/routes/ReadingRoutes.php';

Flight::start();