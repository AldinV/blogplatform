<?php
use OpenApi\Annotations as OA;
/**
 * Posts routes
 */

/**
 * @OA\Get(
 *   path="/posts",
 *   tags={"posts"},
 *   summary="List posts",
 *   @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer", minimum=0, example=50)),
 *   @OA\Parameter(name="offset", in="query", @OA\Schema(type="integer", minimum=0, example=0)),
 *   @OA\Response(
 *     response=200,
 *     description="List of posts",
 *     @OA\JsonContent(type="array", @OA\Items(type="object",
 *       @OA\Property(property="id", type="integer", example=10),
 *       @OA\Property(property="author_id", type="integer", example=1),
 *       @OA\Property(property="author_name", type="string", example="Admin"),
 *       @OA\Property(property="title", type="string", example="Hello"),
 *       @OA\Property(property="content", type="string", example="This is a test post."),
 *       @OA\Property(property="category_id", type="integer", example=2),
 *       @OA\Property(property="category_name", type="string", example="Tech"),
 *       @OA\Property(property="created_at", type="string", example="2025-01-01 12:00:00")
 *     ))
 *   ),
 *   @OA\Response(response=500, description="Unexpected server error")
 * )
 */
Flight::route('GET /posts', function(){
  try {
    $q = Flight::request()->query;
    $limit = isset($q['limit']) ? max(0, (int)$q['limit']) : 50;
    $offset = isset($q['offset']) ? max(0, (int)$q['offset']) : 0;
    Flight::json(Flight::postsService()->listAll($limit, $offset));
  } catch (Throwable $e) {
    apiError($e);
  }
});

/**
 * @OA\Get(
 *   path="/posts/{id}",
 *   tags={"posts"},
 *   summary="Get post by id",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="Post", @OA\JsonContent(type="object")),
 *   @OA\Response(response=404, description="Not found"),
 *   @OA\Response(response=500, description="Unexpected server error")
 * )
 */
Flight::route('GET /posts/@id:[0-9]+', function(string $id){
  $id = (int)$id;
  try {
    $post = Flight::postsService()->getById($id);
    if (!$post) { Flight::json(['error' => 'Post not found'], 404); return; }
    Flight::json($post);
  } catch (Throwable $e) {
    apiError($e);
  }
});

/**
 * @OA\Post(
 *   path="/posts",
 *   tags={"posts"},
 *   summary="Create post",
 *   @OA\RequestBody(required=true, @OA\JsonContent(
 *     required={"author_id","title","content","category_id"},
 *     @OA\Property(property="author_id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Hello"),
 *     @OA\Property(property="content", type="string", example="This is a test post."),
 *     @OA\Property(property="category_id", type="integer", example=2),
 *     @OA\Property(property="tag_ids", type="array", @OA\Items(type="integer"))
 *   )),
 *   @OA\Response(response=201, description="Created post"),
 *   @OA\Response(response=400, description="Validation error"),
 *   @OA\Response(response=500, description="Unexpected server error")
 * )
 */
Flight::route('POST /posts', function(){
  try {
    $data = apiBody();
    Flight::json(Flight::postsService()->create($data), 201);
  } catch (Throwable $e) {
    apiError($e);
  }
});

/**
 * @OA\Put(
 *   path="/posts/{id}",
 *   tags={"posts"},
 *   summary="Update post",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\RequestBody(required=true, @OA\JsonContent(
 *     @OA\Property(property="title", type="string", example="New title"),
 *     @OA\Property(property="content", type="string", example="Updated content"),
 *     @OA\Property(property="category_id", type="integer", example=3),
 *     @OA\Property(property="tag_ids", type="array", @OA\Items(type="integer"))
 *   )),
 *   @OA\Response(response=200, description="Updated post"),
 *   @OA\Response(response=404, description="Not found"),
 *   @OA\Response(response=400, description="Validation error"),
 *   @OA\Response(response=500, description="Unexpected server error")
 * )
 */
Flight::route('PUT /posts/@id:[0-9]+', function(string $id){
  $id = (int)$id;
  try {
    $data = apiBody();
    apiRecord(Flight::postsService()->update($id, $data));
  } catch (Throwable $e) {
    apiError($e);
  }
});

/**
 * @OA\Delete(
 *   path="/posts/{id}",
 *   tags={"posts"},
 *   summary="Delete post",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="Deleted"),
 *   @OA\Response(response=404, description="Not found"),
 *   @OA\Response(response=400, description="Invalid change"),
 *   @OA\Response(response=500, description="Unexpected server error")
 * )
 */
Flight::route('DELETE /posts/@id:[0-9]+', function(string $id){
  $id = (int)$id;
  try {
    apiRecord((Flight::postsService()->delete($id)) ? ['deleted' => 1] : null);
  } catch (Throwable $e) {
    apiError($e);
  }
});

/**
 * @OA\Get(
 *   path="/posts/{id}/tags",
 *   tags={"posts"},
 *   summary="Get tags for a post",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="List of tags"),
 *   @OA\Response(response=404, description="Not found"),
 *   @OA\Response(response=500, description="Unexpected server error")
 * )
 */
Flight::route('GET /posts/@id:[0-9]+/tags', function(string $id){
  $id = (int)$id;
  try {
    Flight::json(Flight::postsService()->getTags($id));
  } catch (Throwable $e) {
    apiError($e);
  }
});

/**
 * @OA\Put(
 *   path="/posts/{id}/tags",
 *   tags={"posts"},
 *   summary="Replace tags for a post",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\RequestBody(required=true, @OA\JsonContent(
 *     required={"tag_ids"},
 *     @OA\Property(property="tag_ids", type="array", @OA\Items(type="integer"))
 *   )),
 *   @OA\Response(response=200, description="OK"),
 *   @OA\Response(response=404, description="Not found"),
 *   @OA\Response(response=400, description="Invalid change"),
 *   @OA\Response(response=500, description="Unexpected server error")
 * )
 */
Flight::route('PUT /posts/@id:[0-9]+/tags', function(string $id){
  $id = (int)$id;
  try {
    $data = apiBody();
    Flight::postsService()->replaceTags($id, $data['tag_ids'] ?? null);
    Flight::json(['ok' => true]);
  } catch (Throwable $e) {
    apiError($e);
  }
});
