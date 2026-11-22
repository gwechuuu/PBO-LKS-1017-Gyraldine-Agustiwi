<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Contracts\BookRepositoryInterface;
use App\Contracts\CategoryRepositoryInterface;
use App\Controllers\BookController;
use App\Controllers\CategoryController;
use App\Core\Container;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Database\Connection;
use App\Exceptions\DatabaseException;
use App\Exceptions\HttpException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Repositories\MySQLBookRepository;
use App\Repositories\MySQLCategoryRepository;
use App\Services\BookService;
use App\Services\CategoryService;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $container = new Container();

    $container->singleton(PDO::class, fn(): PDO => Connection::getInstance());

    // Book dependencies
    $container->singleton(
        BookRepositoryInterface::class,
        fn(Container $c): BookRepositoryInterface => new MySQLBookRepository($c->make(PDO::class))
    );
    $container->singleton(
        BookService::class,
        fn(Container $c): BookService => new BookService($c->make(BookRepositoryInterface::class))
    );
    $container->singleton(
        BookController::class,
        fn(Container $c): BookController => new BookController($c->make(BookService::class))
    );

    // Category dependencies
    $container->singleton(
        CategoryRepositoryInterface::class,
        fn(Container $c): CategoryRepositoryInterface => new MySQLCategoryRepository($c->make(PDO::class))
    );
    $container->singleton(
        CategoryService::class,
        fn(Container $c): CategoryService => new CategoryService($c->make(CategoryRepositoryInterface::class))
    );
    $container->singleton(
        CategoryController::class,
        fn(Container $c): CategoryController => new CategoryController($c->make(CategoryService::class))
    );

    $router = new Router();
    $bookController = $container->make(BookController::class);
    $categoryController = $container->make(CategoryController::class);

    $router->get('/', function (): void {
        Response::json([
            'status' => 'success',
            'message' => 'REST API Perpustakaan aktif',
        ], 200);
    });

    // Book routes
    $router->get('/api/books', fn(Request $req, array $params) => $bookController->index($req));
    $router->get('/api/books/{id}', fn(Request $req, array $params) => $bookController->show($req, $params));
    $router->post('/api/books', fn(Request $req, array $params) => $bookController->store($req));
    $router->put('/api/books/{id}', fn(Request $req, array $params) => $bookController->update($req, $params));
    $router->delete('/api/books/{id}', fn(Request $req, array $params) => $bookController->destroy($req, $params));

    // Category routes
    $router->get('/api/categories', fn(Request $req, array $params) => $categoryController->index($req));
    $router->get('/api/categories/{id}', fn(Request $req, array $params) => $categoryController->show($req, $params));
    $router->post('/api/categories', fn(Request $req, array $params) => $categoryController->store($req));
    $router->put('/api/categories/{id}', fn(Request $req, array $params) => $categoryController->update($req, $params));
    $router->delete('/api/categories/{id}', fn(Request $req, array $params) => $categoryController->destroy($req, $params));

    $request = Request::fromGlobals();
    $router->dispatch($request);
} catch (ValidationException $e) {
    Response::json([
        'status' => 'error',
        'message' => $e->getMessage(),
    ], 422);
} catch (NotFoundException $e) {
    Response::json([
        'status' => 'error',
        'message' => $e->getMessage(),
    ], 404);
} catch (HttpException $e) {
    $payload = [
        'status' => 'error',
        'message' => $e->getMessage(),
    ];
    if ($e->getErrors() !== []) {
        $payload['errors'] = $e->getErrors();
    }
    Response::json($payload, $e->getStatusCode());
} catch (DatabaseException $e) {
    Response::json([
        'status' => 'error',
        'message' => 'Terjadi kesalahan pada database',
    ], 500);
} catch (Throwable $e) {
    Response::json([
        'status' => 'error',
        'message' => 'Terjadi kesalahan pada server',
    ], 500);
}