<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\CategoryService;

class CategoryController
{
    public function __construct(private readonly CategoryService $service) {}

    public function index(Request $request): void
    {
        Response::json([
            'status' => 'success',
            'data' => $this->service->getAllCategories(),
        ], 200);
    }

    public function show(Request $request, array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        Response::json([
            'status' => 'success',
            'data' => $this->service->getCategoryById($id),
        ], 200);
    }

    public function store(Request $request): void
    {
        $data = $request->getJsonBody() ?? $request->all();
        $created = $this->service->createCategory($data);

        Response::json([
            'status' => 'success',
            'message' => 'Kategori berhasil ditambahkan',
            'data' => $created,
        ], 201);
    }

    public function update(Request $request, array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        $data = $request->getJsonBody() ?? $request->all();
        $updated = $this->service->updateCategory($id, $data);

        Response::json([
            'status' => 'success',
            'message' => 'Kategori berhasil diperbarui',
            'data' => $updated,
        ], 200);
    }

    public function destroy(Request $request, array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        $this->service->deleteCategory($id);

        Response::json([
            'status' => 'success',
            'message' => 'Kategori berhasil dihapus',
        ], 200);
    }
}