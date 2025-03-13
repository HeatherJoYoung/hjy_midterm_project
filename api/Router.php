<?php 

$method = $_SERVER['REQUEST_METHOD'];
$request = strtok($_SERVER['REQUEST_URI'], '?');
$controllerDirectory = '/controllers/';

if (!$_SERVER['HTTP_HOST'] === 'localhost') {
	$request = '/hjy_midterm_project/' . $request;
}

echo '$request in Router: ' . $request . '<br>';

switch($request) {
	case 'api':
	case '/hjy_midterm_project/api/':
		require __DIR__ . '/gui/home.php';
		break;
	case 'api/authors':
	case 'api/authors/':
		require __DIR__ . $controllerDirectory . 'AuthorController.php';
		break;
	case 'api/categories':
	case 'api/categories/':
		require __DIR__ . $controllerDirectory . 'CategoryController.php';
		break;
	case 'api/quotes':
	case 'api/quotes/':
		require __DIR__ . $controllerDirectory . 'QuoteController.php';
		break;
	default:
		http_response_code(404);
}
?>