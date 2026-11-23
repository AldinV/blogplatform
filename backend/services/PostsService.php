<?php
require_once __DIR__ . '/../dao/PostsDao.php';
require_once __DIR__ . '/../dao/PostTagsDao.php';
require_once __DIR__ . '/../dao/UsersDao.php';
require_once __DIR__ . '/../dao/CategoriesDao.php';
require_once __DIR__ . '/../dao/TagsDao.php';
require_once __DIR__ . '/BaseService.php';

class PostsService extends BaseService {
  private PostTagsDao $postTags;
  private UsersDao $usersDao;
  private CategoriesDao $categoriesDao;
  private TagsDao $tagsDao;

  public function __construct() {
    parent::__construct(new PostsDao());
    $this->postTags = new PostTagsDao();
    $this->usersDao = new UsersDao();
    $this->categoriesDao = new CategoriesDao();
    $this->tagsDao = new TagsDao();
  }

  protected function validateCreate(array &$data): void {
    $this->validateFields($data, ['title' => 'string', 'content' => 'string', 'author_id' => 'id', 'category_id' => 'id']);
    $this->trimStrings($data, ['title','content']);
    foreach (['author_id','title','content','category_id'] as $f) {
      if (!isset($data[$f]) || $data[$f] === '') throw new InvalidArgumentException("$f is required");
    }
    $this->ensureMaxLength($data, 'title', 200);
    $this->ensureMaxLength($data, 'content', 10000);

    $author = $this->usersDao->getById((int)$data['author_id']);
    if (!$author) throw new InvalidArgumentException('author_id does not reference a valid user');

    $cat = $this->categoriesDao->getById((int)$data['category_id']);
    if (!$cat) throw new InvalidArgumentException('category_id does not reference a valid category');
  }

  protected function validateUpdate(int $id, array &$data): void {
    $this->validateFields($data, ['title' => 'string', 'content' => 'string', 'author_id' => 'id', 'category_id' => 'id']);
    $this->trimStrings($data, ['title','content']);
    $this->ensureMaxLength($data, 'title', 200);
    $this->ensureMaxLength($data, 'content', 10000);
    if (isset($data['author_id'])) {
      $author = $this->usersDao->getById((int)$data['author_id']);
      if (!$author) throw new InvalidArgumentException('author_id does not reference a valid user');
    }
    if (isset($data['category_id'])) {
      $cat = $this->categoriesDao->getById((int)$data['category_id']);
      if (!$cat) throw new InvalidArgumentException('category_id does not reference a valid category');
    }
  }

  public function listAll(int $limit = 50, int $offset = 0, string $orderBy = 'p.created_at DESC'): array {
    return parent::listAll($limit, $offset, $orderBy);
  }

  public function create(array $data): array {
    return $this->saveWithTags($data);
  }

  public function update(int $id, array $data): ?array {
    return $this->saveWithTags($data, $id);
  }

  private function saveWithTags(array $data, ?int $id = null): ?array {
    if (!array_key_exists('tag_ids', $data)) {
      return $id === null ? parent::create($data) : parent::update($id, $data);
    }
    $tagIds = $this->validateTags($data['tag_ids']);
    unset($data['tag_ids']);
    return $this->dao->transaction(function () use ($id, $data, $tagIds) {
      $post = $id === null ? parent::create($data) : parent::update($id, $data);
      if ($post !== null) {
        $this->postTags->replaceTagsForPost((int)$post['id'], $tagIds);
      }
      return $post;
    });
  }

  public function replaceTags(int $postId, $tagIds): void {
    $this->ensurePostExists($postId);
    $this->postTags->replaceTagsForPost($postId, $this->validateTags($tagIds));
  }

  private function validateTags($tagIds): array {
    if (!is_array($tagIds) || array_values($tagIds) !== $tagIds) {
      throw new InvalidArgumentException('tag_ids must be an array of tag IDs');
    }
    foreach ($tagIds as $tid) {
      if (!is_int($tid) || $tid <= 0) {
        throw new InvalidArgumentException('tag_ids must contain positive integers');
      }
      $tag = $this->tagsDao->getById((int)$tid);
      if (!$tag) {
        throw new InvalidArgumentException("Tag ID $tid does not exist");
      }
    }
    return array_values(array_unique($tagIds));
  }

  private function ensurePostExists(int $postId): void {
    if (!$this->dao->getById($postId)) {
      throw new OutOfBoundsException('Post not found');
    }
  }

  public function getTags(int $postId): array {
    $this->ensurePostExists($postId);
    return $this->postTags->getTagsForPost($postId);
  }
}
