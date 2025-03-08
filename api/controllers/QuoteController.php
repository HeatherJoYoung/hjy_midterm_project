<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
  }

  include_once(__DIR__ . '/../models/Quote.php');

  switch ($method) {
    case 'GET':
      getQuotes();
      break;
    case 'POST':
      createQuote();
      break;
    case 'PUT':
      updateQuote();
      break;
    case 'DELETE':
      deleteQuote();
      break;
  }
  
  function getQuotes () {

    $queryString = $_SERVER['QUERY_STRING'];

    parse_str(html_entity_decode($queryString), $vars);

    $id = isset($vars['id']) ? $vars['id'] : '';
    $filters = [];
    
    if (isset($vars['category_id'])) {
      $filters['q.category_id'] = $vars['category_id'];
    }

    if (isset($vars['author_id'])) {
      $filters['q.author_id'] = $vars['author_id'];
    }

    $quoteObject = new Quote();

    $result = $id ? $quoteObject->read_single($id) : $quoteObject->read($filters);
    
    $num = $result->rowCount();

    if ($num > 0) {

      $cat_arr = array();

      while($row = $result->fetch(PDO::FETCH_ASSOC)) {

        extract($row);
       
        $cat_item = array(
            'id' => $id,
            'quote' => htmlspecialchars_decode($quote),
            'category' => $category,
            'author' => $author
        );

        array_push($cat_arr, $cat_item);
      }

      echo json_encode($cat_arr);

    } else {

      echo json_encode(array('message' => 'No Quotes Found'));
    }
  }

  function createQuote() {

    $requestBody = json_decode(file_get_contents('php://input'), true);
    $quotation = $requestBody['quote'];
    $category_id = $requestBody['category_id'];
    $author_id = $requestBody['author_id'];

    if (!$quotation || !$category_id || !$author_id) {
      http_response_code(404);
      echo json_encode(array('message' => 'Missing Required Parameters'));
      return;
    }

    $quote = new Quote();
    $quote->quotation = $quotation;
    $quote->author_id = $author_id;
    $quote->category_id = $category_id;
    $result = $quote->create();

    echo $result['status'] == 'success' ? json_encode($quote) : json_encode(array('message' => $result['message']));
  }

  function updateQuote() {

    $requestBody = json_decode(file_get_contents('php://input'), true);
    $quotation = $requestBody['quote'];
    $id = $requestBody['id'];
    $author_id = $requestBody['author_id'];
    $category_id = $requestBody['category_id'];

    if (is_null($quotation) || is_null($id) || is_null($author_id) || is_null($category_id)) {
      http_response_code(400);
      echo json_encode(array('message' => 'Missing Required Parameters'));
      return;
    }

    $quote = new Quote();
    $quote->id = $id;
    $quote->quotation = $quotation;
    $quote->author_id = $author_id;
    $quote->category_id = $category_id;
    
    $result = $quote->update();

    echo $result ? json_encode($quote) : 'failed to update category';
  }

  function deleteQuote() {

    $queryString = $_SERVER['QUERY_STRING'];
    parse_str(html_entity_decode($queryString), $vars);
    $id = isset($vars['id']) ? $vars['id'] : '';

    if (is_null($id)) {
      http_response_code(400);
      echo json_encode(array('message' => 'Missing Required Parameters'));
      return;
    }

    $quote = new Quote();

    $result = $quote->delete($id);

    return $result ? json_encode($id) : 'Failed to delete record ' . $id;
  }
  
?>