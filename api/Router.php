<?php 

$method = $_SERVER['REQUEST_METHOD'];
$request = strtok($_SERVER['REQUEST_URI'], '?');
$controllerDirectory = '/api/controllers/';

switch($request) {
	case '/hjy_midterm_project/api/gui/style.css':
		require __DIR__ . '/gui/style.css';
		break;
	case '/hjy_midterm_project/gui/home.js':
		require __DIR__ . '/gui/home.js';
		break;
	case '/hjy_midterm_project/api':
	case '/hjy_midterm_project/api/':
		require __DIR__ . '/gui/home.php';
		break;
	case '/hjy_midterm_project/api/authors':
	case '/hjy_midterm_project/api/authors/':
		require __DIR__ . $controllerDirectory . 'AuthorController.php';
		break;
	case '/hjy_midterm_project/api/categories':
	case '/hjy_midterm_project/api/categories/':
		require __DIR__ . $controllerDirectory . 'CategoryController.php';
		break;
	case '/hjy_midterm_project/api/controllers/QuoteController.php/':
		require __DIR__ . $controllerDirectory . 'QuoteController.php';
		break;
	case '/hjy_midterm_project/api/quotes':
	case '/hjy_midterm_project/api/quotes/':
		require __DIR__ . $controllerDirectory . 'QuoteController.php';
		break;
	default:
		http_response_code(404);
}
?>