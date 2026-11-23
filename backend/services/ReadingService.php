<?php
class ReadingService {
  public function search($topic): ?array {
    if (!is_string($topic) || trim($topic) === '' || mb_strlen($topic) > 100) {
      throw new InvalidArgumentException('q must contain 1 to 100 characters');
    }

    $params = http_build_query([
      'q' => trim($topic),
      'fields' => 'key,title,author_name',
      'limit' => 5,
    ]);
    $client = curl_init('https://openlibrary.org/search.json?' . $params);
    curl_setopt_array($client, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CONNECTTIMEOUT => 7,
      CURLOPT_TIMEOUT => 10,
      CURLOPT_USERAGENT => 'StudentBlogPlatform/1.0',
      CURLOPT_HTTPHEADER => ['Accept: application/json'],
    ]);
    $body = curl_exec($client);
    $status = curl_getinfo($client, CURLINFO_RESPONSE_CODE);
    $curlError = curl_errno($client);

    $result = is_string($body) && $status === 200 ? json_decode($body, true) : null;
    if (!is_array($result['docs'] ?? null)) {
      $message = sprintf("[%s] Open Library search failed (HTTP %d, cURL %d)\n", date(DATE_ATOM), $status, $curlError);
      $directory = __DIR__ . '/../logs';
      if ((!is_dir($directory) && !@mkdir($directory) && !is_dir($directory))
          || !@error_log($message, 3, $directory . '/error.log')) {
        error_log(trim($message));
      }
      return null;
    }

    $books = [];
    foreach ($result['docs'] as $book) {
      if (!is_string($book['title'] ?? null)
          || !is_string($book['key'] ?? null)
          || !preg_match('~^(?:/works/)?(OL[0-9]+W)$~D', $book['key'], $key)) {
        continue;
      }
      $books[] = [
        'title' => $book['title'],
        'authors' => array_values(array_filter((array)($book['author_name'] ?? []), 'is_string')),
        'url' => 'https://openlibrary.org/works/' . $key[1],
      ];
    }
    return $books;
  }
}
