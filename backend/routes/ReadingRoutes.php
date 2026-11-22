<?php
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *   path="/related-reading",
 *   tags={"related reading"},
 *   summary="Find books about a blog topic",
 *   @OA\Parameter(name="q", in="query", required=true, @OA\Schema(type="string", minLength=1, maxLength=100, example="PHP programming")),
 *   @OA\Response(response=200, description="Up to five related books",
 *     @OA\JsonContent(type="object",
 *       @OA\Property(property="books", type="array", @OA\Items(type="object",
 *         @OA\Property(property="title", type="string"),
 *         @OA\Property(property="authors", type="array", @OA\Items(type="string")),
 *         @OA\Property(property="url", type="string", format="uri")
 *       ))
 *     )
 *   ),
 *   @OA\Response(response=400, description="Invalid search topic"),
 *   @OA\Response(response=503, description="Related reading is temporarily unavailable"),
 *   @OA\Response(response=500, description="Unexpected server error")
 * )
 */
Flight::route('GET /related-reading', function () {
  try {
    $books = Flight::readingService()->search(Flight::request()->query['q'] ?? null);
    if ($books === null) {
      Flight::json(['error' => 'Related reading is temporarily unavailable. Please try again later.'], 503);
      return;
    }
    Flight::json(['books' => $books]);
  } catch (Throwable $error) {
    apiError($error);
  }
});
