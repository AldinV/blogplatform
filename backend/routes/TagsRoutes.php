<?php
use OpenApi\Annotations as OA;
/**
 * Tags routes
 */

/**
 * @OA\Get(
 *   path="/tags",
 *   tags={"tags"},
 *   summary="List tags",
 *   @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer", minimum=0, example=50)),
 *   @OA\Parameter(name="offset", in="query", @OA\Schema(type="integer", minimum=0, example=0)),
 *   @OA\Response(
 *     response=200,
 *     description="List of tags",
 *     @OA\JsonContent(type="array", @OA\Items(type="object",
 *       @OA\Property(property="id", type="integer", example=1),
 *       @OA\Property(property="name", type="string", example="PHP"),
 *       @OA\Property(property="created_at", type="string", example="2025-01-01 12:00:00")
 *     ))
 *   ),
 *   @OA\Response(response=500, description="Unexpected server error")
 * )
 */
Flight::route('GET /tags', function(){
  try {
    $q = Flight::request()->query;
    $limit = isset($q['limit']) ? max(0, (int)$q['limit']) : 50;
    $offset = isset($q['offset']) ? max(0, (int)$q['offset']) : 0;
    Flight::json(Flight::tagsService()->listAll($limit, $offset));
  } catch (Throwable $e) {
    apiError($e);
  }
});

/**
 * @OA\Get(
 *   path="/tags/{id}",
 *   tags={"tags"},
 *   summary="Get tag by id",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="Tag", @OA\JsonContent(type="object")),
 *   @OA\Response(response=404, description="Not found"),
 *   @OA\Response(response=500, description="Unexpected server error")
 * )
 */
Flight::route('GET /tags/@id:[0-9]+', function(string $id){
  $id = (int)$id;
  try {
    $tag = Flight::tagsService()->getById($id);
    if (!$tag) { Flight::json(['error' => 'Tag not found'], 404); return; }
    apiRecord($tag);
  } catch (Throwable $e) {
    apiError($e);
  }
});

/**
 * @OA\Get(
 *   path="/tags/by-name",
 *   tags={"tags"},
 *   summary="Get tag by name",
 *   @OA\Parameter(name="name", in="query", required=true, @OA\Schema(type="string", example="PHP")),
 *   @OA\Response(response=200, description="Tag", @OA\JsonContent(type="object")),
 *   @OA\Response(response=404, description="Not found"),
 *   @OA\Response(response=400, description="Validation error"),
 *   @OA\Response(response=500, description="Unexpected server error")
 * )
 */
Flight::route('GET /tags/by-name', function(){
  try {
    $name = Flight::request()->query['name'] ?? null;
    if (!is_string($name) || trim($name) === '') { Flight::json(['error' => 'name is required'], 400); return; }
    $tag = Flight::tagsService()->getByName(trim($name));
    apiRecord($tag);
  } catch (Throwable $e) {
    apiError($e);
  }
});

/**
 * @OA\Post(
 *   path="/tags",
 *   tags={"tags"},
 *   summary="Create tag",
 *   @OA\RequestBody(required=true, @OA\JsonContent(
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="PHP")
 *   )),
 *   @OA\Response(response=201, description="Created tag"),
 *   @OA\Response(response=400, description="Validation error"),
 *   @OA\Response(response=500, description="Unexpected server error")
 * )
 */
Flight::route('POST /tags', function(){
  try {
    $data = apiBody();
    Flight::json(Flight::tagsService()->create($data), 201);
  } catch (Throwable $e) {
    apiError($e);
  }
});

/**
 * @OA\Put(
 *   path="/tags/{id}",
 *   tags={"tags"},
 *   summary="Update tag",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\RequestBody(required=true, @OA\JsonContent(
 *     @OA\Property(property="name", type="string", example="Backend")
 *   )),
 *   @OA\Response(response=200, description="Updated tag"),
 *   @OA\Response(response=404, description="Not found"),
 *   @OA\Response(response=400, description="Validation error"),
 *   @OA\Response(response=500, description="Unexpected server error")
 * )
 */
Flight::route('PUT /tags/@id:[0-9]+', function(string $id){
  $id = (int)$id;
  try {
    $data = apiBody();
    apiRecord(Flight::tagsService()->update($id, $data));
  } catch (Throwable $e) {
    apiError($e);
  }
});

/**
 * @OA\Delete(
 *   path="/tags/{id}",
 *   tags={"tags"},
 *   summary="Delete tag",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="Deleted"),
 *   @OA\Response(response=404, description="Not found"),
 *   @OA\Response(response=400, description="Invalid change"),
 *   @OA\Response(response=500, description="Unexpected server error")
 * )
 */
Flight::route('DELETE /tags/@id:[0-9]+', function(string $id){
  $id = (int)$id;
  try {
    apiRecord((Flight::tagsService()->delete($id)) ? ['deleted' => 1] : null);
  } catch (Throwable $e) {
    apiError($e);
  }
});
