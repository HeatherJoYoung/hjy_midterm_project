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
  include_once(__DIR__ . '/../models/Category.php');

  switch ($method) {
    case 'GET':
      getCategories();
      break;
    case 'POST':
      createCategory();
      break;
    case 'PUT':
      updateCategory();
      break;
    case 'DELETE':
      deleteCategory();
      break;
  }
  
  function getCategories () {

    $queryString = $_SERVER['QUERY_STRING'];

    parse_str(html_entity_decode($queryString), $vars);

    $id = isset($vars['id']) ? $vars['id'] : '';
    $category = new Category();

    $result = $id ? $category->read_single($id): $category->read();
    
    $num = $result->rowCount();

    if ($num > 0) {

      $cat_arr = array();

      while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $cat_item = array(
            'id' => $id,
            'category' => $category
        );

        array_push($cat_arr, $cat_item);
      }

      echo $id ? json_encode($cat_arr[0]) : json_encode($cat_arr);

    } else {

      echo json_encode(array('message' => 'category_id Not Found'));
    }
  }

  function createCategory() {

		$requestBody = json_decode(file_get_contents('php://input'), true);
    $categoryName = $requestBody['category'];

    if (!$categoryName) {
      return json_encode(array('message' => 'Missing Required Parameters'));
    }

    $category = new Category();
    $category->category = htmlspecialchars(strip_tags($categoryName));
    
    $result = $category->create();

    $responseBody = $result['status'] == 'success' ? $category : array('message'=>$result['message']);

		echo json_encode($responseBody);
  }

  function updateCategory() {
		
    $requestBody = json_decode(file_get_contents('php://input'), true);
    $categoryName = $requestBody['category'];
    $categoryId = $requestBody['id'];

    if (is_null($categoryName) || is_null($categoryId)) {
      echo json_encode(array('message' => 'Missing Required Parameters'));
      return;
    }

    $category = new Category();
    $category->category = htmlspecialchars(strip_tags($categoryName));
    $category->id = htmlspecialchars(strip_tags($categoryId));
    
    $result = $category->update();

    $responseBody = $result['status'] == 'success' ? $category : array('message' => $result['message']);

		echo json_encode($responseBody);
  }

  function deleteCategory() {
		
    $queryString = $_SERVER['QUERY_STRING'];
    parse_str(html_entity_decode($queryString), $vars);
    $id = isset($vars['id']) ? $vars['id'] : '';

    if (is_null($id)) {
      echo json_encode(array('message' => 'Missing Required Parameters'));
      return;
    }

    $category = new Category();

    $result = $category->delete($id);

    $responseBody = $result['status'] == 'success' ? array('id'=>$id) : array('message'=>$result['message']);

		echo json_encode($responseBody);
  }
  
?>