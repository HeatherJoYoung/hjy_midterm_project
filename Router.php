<?php 

$method = $_SERVER['REQUEST_METHOD'];
$request = strtok($_SERVER['REQUEST_URI'], '?');
$controllerDirectory = '/api/controllers/';

switch($request) {
    case '/hjy_midterm_project':
    case '/hjy_midterm_project/api':
        break;
    case '/hjy_midterm_project/api/authors/':
        require __DIR__ . $controllerDirectory . 'AuthorController.php';
        break;
    case '/hjy_midterm_project/api/categories/':
        require __DIR__ . $controllerDirectory . 'CategoryController.php';
        break;
    case '/hjy_midterm_project/api/quotes/':
        require __DIR__ . $controllerDirectory . 'QuoteController.php';
        break;
    default:
        http_response_code(404);
}
?>