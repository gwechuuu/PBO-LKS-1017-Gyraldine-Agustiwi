<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Exceptions\DatabaseException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Services\BookService;
use Throwable;

class BookController
{
    private BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function index(Request $request): void
    {
        try {
            $books = $this->bookService->getAllBooks();

            Response::json([
                'status' => 'success',
                'data' => $books,
            ], 200);
        } catch (DatabaseException $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            Response::json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }

    /**
     * @param array<string, mixed> $params
     */
    public function show(Request $request, array $params): void
    {
        try {
            $id = (int) ($params['id'] ?? 0);
            $book = $this->bookService->getBookById($id);

            Response::json([
                'status' => 'success',
                'data' => $book,
            ], 200);
        } catch (ValidationException $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
            ], 422);
        } catch (NotFoundException $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 404);
        } catch (DatabaseException $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            Response::json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }

    public function store(Request $request): void
    {
        try {
            $created = $this->bookService->createBook($request->getBody());

            Response::json([
                'status' => 'success',
                'message' => 'Buku berhasil ditambahkan',
                'data' => $created,
            ], 201);
        } catch (ValidationException $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
            ], 422);
        } catch (DatabaseException $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            Response::json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }

    /**
     * @param array<string, mixed> $params
     */
    public function update(Request $request, array $params): void
    {
        try {
            $id = (int) ($params['id'] ?? 0);
            $updated = $this->bookService->updateBook($id, $request->getBody());

            Response::json([
                'status' => 'success',
                'message' => 'Buku berhasil diperbarui',
                'data' => $updated,
            ], 200);
        } catch (ValidationException $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
            ], 422);
        } catch (NotFoundException $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 404);
        } catch (DatabaseException $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            Response::json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }

    /**
     * @param array<string, mixed> $params
     */
    public function destroy(Request $request, array $params): void
    {
        try {
            $id = (int) ($params['id'] ?? 0);
            $this->bookService->deleteBook($id);

            Response::json([
                'status' => 'success',
                'message' => 'Buku berhasil dihapus',
            ], 200);
        } catch (ValidationException $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
            ], 422);
        } catch (NotFoundException $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 404);
        } catch (DatabaseException $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            Response::json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }
} 