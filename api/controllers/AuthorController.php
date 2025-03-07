<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once(__DIR__ . '/../models/Author.php');

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

    $queryString = $_SERVER['QUERY_STRING'];

    parse_str(html_entity_decode($queryString), $vars);

    $id = isset($vars['id']) ? $vars['id'] : '';
    $author = new Author();

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

        $errorMsg = $id ? 'Author ' . $id . ' not found' : 'No authors found';
        echo json_encode(array('message' => $errorMsg));
    }
  }

  function createAuthor() {

    $requestBody = json_decode(file_get_contents('php://input'), true);
    $authorName = $requestBody['author'];
    $author = new Author();
    $author->name = $authorName;

    echo 'authorName: ' . $authorName;
    
    $result = $author->create();

    return $result ? json_encode($author) : 'failed to create author';
  }

  function updateAuthor() {

    $requestBody = json_decode(file_get_contents('php://input'), true);
    $authorName = $requestBody['author'];
    $authorId = $requestBody['id'];

    if (is_null($authorName) || is_null($authorId)) {

      http_response_code(400);

      return 'ID and Author fields are required to make an update.'; 
    }

    $author = new Author();
    $author->name = $authorName;
    $author->id = $authorId;
    
    $result = $author->update();

    return $result ? json_encode($author) : 'failed to update author';
  }

  function deleteAuthor() {

    $queryString = $_SERVER['QUERY_STRING'];

    parse_str(html_entity_decode($queryString), $vars);

    $id = isset($vars['id']) ? $vars['id'] : '';
    $author = new Author();

    $result = $author->delete($id);

    return $result ? json_encode($id) : 'Failed to delete author ' . $id;
  }
  
?>