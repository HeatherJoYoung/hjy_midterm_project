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

    $getById = isset($vars['id']) ? (int) $vars['id'] : '';
    $filters = [];
    $random = !empty($vars['random']) ? true : false;
    
    if (isset($vars['category_id'])) {
      $filters['q.category_id'] = $vars['category_id'];
    }

    if (isset($vars['author_id'])) {
      $filters['q.author_id'] = $vars['author_id'];
    }

    $quoteObject = new Quote();

    $result = $getById ? $quoteObject->read_single($getById) : $quoteObject->read($filters, $random);
    
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

      echo $getById ? json_encode($cat_arr[0]) : json_encode($cat_arr);

    } else {

      echo json_encode(array('message' => 'No Quotes Found'));
    }
  }

  function createQuote() {
		
    $requestBody = json_decode(file_get_contents('php://input'), true);
    $quote = isset($requestBody['quote']) ? $requestBody['quote'] : '';
    $category_id = isset($requestBody['category_id']) ? $requestBody['category_id'] : '';
    $author_id = isset($requestBody['author_id']) ? $requestBody['author_id'] : '';

    if (!$quote || !$category_id || !$author_id) {
      echo json_encode(array('message' => 'Missing Required Parameters'));
      return;
    }

    $quoteObj = new Quote();
    $quoteObj->quote = $quote;
    $quoteObj->author_id = $author_id;
    $quoteObj->category_id = $category_id;
    $result = $quoteObj->create();

    echo $result['status'] == 'success' ? json_encode($quoteObj) : json_encode(array('message' => $result['message']));
  }

  function updateQuote() {
		
    $requestBody = json_decode(file_get_contents('php://input'), true);
    $quote = isset($requestBody['quote']) ? $requestBody['quote'] : '';
    $id = isset($requestBody['id']) ? (int) $requestBody['id'] : '';
    $author_id = isset($requestBody['author_id']) ? (int) $requestBody['author_id'] : '';
    $category_id = isset($requestBody['category_id']) ? (int) $requestBody['category_id'] : '';

    if (!$quote || !$id || !$author_id || !$category_id) {
      echo json_encode(array('message' => 'Missing Required Parameters'));
      return;
    }

    $quoteObj = new Quote();
    $quoteObj->id = $id;
    $quoteObj->quote = htmlspecialchars(strip_tags($quote));
    $quoteObj->author_id = $author_id;
    $quoteObj->category_id = $category_id;
    
    $result = $quoteObj->update();

    echo $result['status'] == 'success' ? json_encode($quoteObj) : json_encode(array('message'=>$result['message']));
  }

  function deleteQuote() {
		
    $queryString = $_SERVER['QUERY_STRING'];
    parse_str(html_entity_decode($queryString), $vars);
    $id = isset($vars['id']) ? (int) $vars['id'] : '';

    if (!$id) {
      echo json_encode(array('message' => 'Missing Required Parameters'));
      return;
    }

    $quoteObj = new Quote();

    $result = $quoteObj->delete($id);

    $responseBody =  $result['status'] == 'success' ? array('id'=>$id) : array('message'=>$result['message']);

		echo json_encode($responseBody);
  }
  
?>