<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

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

      echo json_encode($cat_arr);

    } else {

      echo json_encode(array('message' => 'No Categories Found'));
    }
  }

  function createCategory() {

    $requestBody = json_decode(file_get_contents('php://input'), true);
    $categoryName = $requestBody['category'];
    $category = new Category();
    $category->name = $categoryName;
    
    $result = $category->create();

    return $result ? json_encode($category) : 'failed to create category';
  }

  function updateCategory() {

    $requestBody = json_decode(file_get_contents('php://input'), true);
    $categoryName = $requestBody['category'];
    $categoryId = $requestBody['id'];

    if (is_null($categoryName) || is_null($categoryId)) {

      http_response_code(400);

      return 'ID and Category fields are required to make an update.'; 
    }

    $category = new Category();
    $category->name = $categoryName;
    $category->id = $categoryId;
    
    $result = $category->update();

    return $result ? json_encode($category) : 'failed to update category';
  }

  function deleteCategory() {

    $queryString = $_SERVER['QUERY_STRING'];

    parse_str(html_entity_decode($queryString), $vars);

    $id = isset($vars['id']) ? $vars['id'] : '';
    $category = new Category();

    $result = $category->delete($id);

    return $result ? json_encode($id) : 'Failed to delete record ' . $id;
  }
  
?>