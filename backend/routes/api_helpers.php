<?php
function apiBody(): array {
  $body = json_decode(Flight::request()->getBody());
  if (!$body instanceof stdClass) {
    throw new InvalidArgumentException('Request body must be a JSON object');
  }
  return (array)$body;
}

function apiRecord(?array $record): void {
  if ($record === null) {
    throw new OutOfBoundsException('Record not found');
  }
  Flight::json($record);
}

function apiError(Throwable $error): void {
  if ($error instanceof InvalidArgumentException) {
    Flight::json(['error' => $error->getMessage()], 400);
  } elseif ($error instanceof OutOfBoundsException) {
    Flight::json(['error' => $error->getMessage()], 404);
  } elseif ($error instanceof PDOException && ($error->errorInfo[0] ?? null) === '23000') {
    Flight::json(['error' => 'The change conflicts with existing records'], 400);
  } else {
    error_log((string)$error);
    Flight::json(['error' => 'An unexpected server error occurred'], 500);
  }
}
