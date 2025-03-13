<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

	$method = $_SERVER['REQUEST_METHOD'];

  if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
  }

	include_once(__DIR__ . '/../config/Database.php');
  include_once(__DIR__ . '/../models/Author.php');

	$database = new Database();
	$db = $database->connect();

  switch ($method) {
    case 'GET':
      getAuthors();
      break;
    case 'POST':
      createAuthor();
      break;
    case 'PUT':
      updateAuthor();
      break;
    case 'DELETE':
      deleteAuthor();
      break;
  }
  
  function getAuthors () {
		GLOBAL $db;
    $queryString = $_SERVER['QUERY_STRING'];

    parse_str(html_entity_decode($queryString), $vars);

    $id = isset($vars['id']) ? $vars['id'] : '';
    $author = new Author($db);

    $result = $id ? $author->read_single($id): $author->read();
    
    $num = $result->rowCount();

    if ($num > 0) {

      $cat_arr = array();

      while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $cat_item = array(
            'id' => $id,
            'author' => $author
        );

        array_push($cat_arr, $cat_item);
      }

      echo json_encode($cat_arr);

    } else {

        echo json_encode(array('message' => 'author_id Not Found'));
    }
  }

  function createAuthor() {
		GLOBAL $db;
    $requestBody = json_decode(file_get_contents('php://input'), true);
    $authorName = $requestBody['author'];
    
    if (!$authorName) {
      http_response_code(400);
      echo json_encode(array('message' => 'Missing Required Parameters'));
      return;
    }

    $author = new Author($db);
    $author->name = htmlspecialchars(strip_tags($authorName));

    $result = $author->create();

    $responseBody = $result['status'] == 'success' ? $author : array('message' => $result['message']);

    echo json_encode($responseBody);
  }

  function updateAuthor() {
		GLOBAL $db;
    $requestBody = json_decode(file_get_contents('php://input'), true);
    $authorName = $requestBody['author'];
    $authorId = $requestBody['id'];

    if (!$authorName || !$authorId) {
      http_response_code(400);
      echo 'Missing Required Parameters';
      return; 
    }

    $author = new Author($db);
    $author->name = htmlspecialchars(strip_tags($authorName));
    $author->id = htmlspecialchars(strip_tags($authorId));
    
    $result = $author->update();

    $responseBody = $result['status'] == 'success' ? $author : array('message'=>$result['message']);

    echo json_encode($responseBody);
  }

  function deleteAuthor() {
		GLOBAL $db;
    $queryString = $_SERVER['QUERY_STRING'];
    parse_str(html_entity_decode($queryString), $vars);
    $id = isset($vars['id']) ? $vars['id'] : '';
    
    if (!$id) {
      http_response_code(400);
      echo json_encode(array('message' => 'Missing Required Parameters'));
      return;
    }

    $author = new Author($db);

    $result = $author->delete($id);

    $responseBody = $result['status'] == 'success' ? $id : array('message'=>$result['message']);

    echo json_encode($responseBody);
  }
  
?>