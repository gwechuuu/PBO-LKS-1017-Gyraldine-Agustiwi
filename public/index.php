<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Contracts\BookRepositoryInterface;
use App\Controllers\BookController;
use App\Core\Container;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Database\Connection;
use App\Repositories\MySQLBookRepository;
use App\Services\BookService;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$container = new Container();

$container->singleton(PDO::class, fn(): PDO => Connection::getInstance());
$container->singleton(BookRepositoryInterface::class, fn(Container $c): BookRepositoryInterface => new MySQLBookRepository($c->make(PDO::class)));
$container->singleton(BookService::class, fn(Container $c): BookService => new BookService($c->make(BookRepositoryInterface::class)));
$container->singleton(BookController::class, fn(Container $c): BookController => new BookController($c->make(BookService::class)));

$router = new Router();
$controller = $container->make(BookController::class);

$router->get('/', function (): void {
    Response::json([
        'status' => 'success',
        'message' => 'REST API Perpustakaan aktif',
    ], 200);
});

$router->get('/api/books', fn(Request $req, array $params) => $controller->index($req));
$router->get('/api/books/{id}', fn(Request $req, array $params) => $controller->show($req, $params));
$router->post('/api/books', fn(Request $req, array $params) => $controller->store($req));
$router->put('/api/books/{id}', fn(Request $req, array $params) => $controller->update($req, $params));
$router->delete('/api/books/{id}', fn(Request $req, array $params) => $controller->destroy($req, $params));

$request = Request::fromGlobals();
$router->dispatch($request);