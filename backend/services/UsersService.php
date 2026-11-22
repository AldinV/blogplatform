<?php
require_once __DIR__ . '/../dao/UsersDao.php';
require_once __DIR__ . '/BaseService.php';

class UsersService extends BaseService {
  public function __construct() { parent::__construct(new UsersDao()); }

  protected function validateCreate(array &$data): void {
    $this->validateFields($data, ['name' => 'string', 'email' => 'string', 'role' => 'string', 'password' => 'string']);
    $this->trimStrings($data, ['name','email','role']);
    if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
      throw new InvalidArgumentException('name, email and password are required');
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      throw new InvalidArgumentException('Invalid email');
    }
    $this->ensureMaxLength($data, 'name', 120);
    $this->ensureMaxLength($data, 'email', 190);
    $data['role'] = $data['role'] ?? 'user';
    $this->ensureEnum($data, 'role', ['user','admin']);

    $this->hashPassword($data);

    /** @var UsersDao $dao */
    $dao = $this->dao;
    if ($dao->getByEmail($data['email'])) {
      throw new InvalidArgumentException('Email already in use');
    }
  }

  protected function validateUpdate(int $id, array &$data): void {
    $this->validateFields($data, ['name' => 'string', 'email' => 'string', 'role' => 'string', 'password' => 'string']);
    $this->trimStrings($data, ['name','email','role']);
    if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      throw new InvalidArgumentException('Invalid email');
    }
    $this->ensureMaxLength($data, 'name', 120);
    $this->ensureMaxLength($data, 'email', 190);
    if (isset($data['role'])) {
      $this->ensureEnum($data, 'role', ['user','admin']);
    }

    $this->hashPassword($data);

    if (isset($data['email'])) {
      /** @var UsersDao $dao */
      $dao = $this->dao;
      $existing = $dao->getByEmail($data['email']);
      if ($existing && (int)$existing['id'] !== $id) {
        throw new InvalidArgumentException('Email already in use');
      }
    }
  }

  public function getById(int $id): ?array {
    return $this->withoutPasswordHash(parent::getById($id));
  }

  public function create(array $data): array {
    return $this->withoutPasswordHash(parent::create($data));
  }

  public function update(int $id, array $data): ?array {
    return $this->withoutPasswordHash(parent::update($id, $data));
  }

  private function withoutPasswordHash(?array $user): ?array {
    unset($user['password_hash']);
    return $user;
  }

  private function hashPassword(array &$data): void {
    if (isset($data['password'])) {
      if (strlen($data['password']) > 72 || strpos($data['password'], "\0") !== false) {
        throw new InvalidArgumentException('Password must be at most 72 bytes and contain no null bytes');
      }
      $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
      unset($data['password']);
    }
  }

  public function findByEmail(string $email): ?array {
    return $this->withoutPasswordHash($this->dao->getByEmail($email));
  }
}
