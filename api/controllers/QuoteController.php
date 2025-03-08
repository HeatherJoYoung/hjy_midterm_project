<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

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
    $quote = new Quote();

    $result = $id ? $quote->read_single($id): $quote->read();
    
    $num = $result->rowCount();

    if ($num > 0) {

      $cat_arr = array();

      while($row = $result->fetch(PDO::FETCH_ASSOC)) {

        extract($row);
       
        $cat_item = array(
            'id' => $id,
            'quotation' => htmlspecialchars_decode($quotation),
            'category' => $category,
            'author' => $author
        );

        array_push($cat_arr, $cat_item);
      }

      echo json_encode($cat_arr);

    } else {

      echo json_encode(array('message' => 'No quotes found'));
    }
  }

  function createQuote() {

    $requestBody = json_decode(file_get_contents('php://input'), true);
    $quotation = $requestBody['quote'];
    $category_id = $requestBody['category_id'];
    $author_id = $requestBody['author_id'];
    $quote = new Quote();
    $quote->quotation = $quotation;
    $quote->author_id = $author_id;
    $quote->category_id = $category_id;
    $result = $quote->create();

    return $result ? json_encode($quote) : 'failed to create quote';
  }

  function updateQuote() {

    $requestBody = json_decode(file_get_contents('php://input'), true);
    $quotation = $requestBody['quote'];
    $id = $requestBody['id'];
    $author_id = $requestBody['author_id'];
    $category_id = $requestBody['category_id'];

    if (is_null($quotation) || is_null($id) || is_null($author_id) || is_null($category_id)) {

      http_response_code(400);

      return 'ID and Category fields are required to make an update.'; 
    }

    $quote = new Quote();
    $quote->id = $id;
    $quote->quotation = $quotation;
    $quote->author_id = $author_id;
    $quote->category_id = $category_id;
    
    $result = $quote->update();

    return $result ? json_encode($quote) : 'failed to update category';
  }

  function deleteQuote() {

    $queryString = $_SERVER['QUERY_STRING'];

    parse_str(html_entity_decode($queryString), $vars);

    $id = isset($vars['id']) ? $vars['id'] : '';
    $quote = new Quote();

    $result = $quote->delete($id);

    return $result ? json_encode($id) : 'Failed to delete record ' . $id;
  }
  
?>