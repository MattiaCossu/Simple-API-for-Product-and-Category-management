<?php

declare(strict_types=1);
//autoloader for classes
spl_autoload_register(function ($class) {
    $path = __DIR__ . "/src/";
    $file = str_replace("\\", "/", $class) . ".php";
    
    if (file_exists($path . "gateway/" . $file)) {
        require_once $path . "gateway/" . $file;
    } elseif (file_exists($path . "controller/" . $file)) {
        require_once $path . "controller/" . $file;
    } elseif (file_exists($path . $file)) {
        require_once $path . $file;
    }
});

//error handling
set_error_handler([ErrorHandler::class, 'handleError']);
set_exception_handler([ErrorHandler::class, 'handleExeption']);

header('Content-Type: application/json; charset=utf-8');

$parts = explode('/', $_SERVER['REQUEST_URI']);
$database = new Database();
$value = $parts[2] ?? null;

//routing
switch ($parts[1]) {
    case 'products':
        $getway = new ProductGateway($database);
        $controller = new ProductController($getway);
        $controller->processRequest($_SERVER['REQUEST_METHOD'], $value);
        break;
    case 'categories':
        $getway = new CategoryGateway($database);
        $controller = new CategoryController($database);
        $controller->processRequest($_SERVER['REQUEST_METHOD'], $value);
        break;
    default:
        http_response_code(404);
        exit;
}
