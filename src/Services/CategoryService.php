<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\CategoryRepositoryInterface;
use App\Exceptions\ConflictException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Models\Category;

class CategoryService
{
    public function __construct(private readonly CategoryRepositoryInterface $repository) {}

    /** @return array<int, array<string, mixed>> */
    public function getAllCategories(): array
    {
        return array_map(
            fn(Category $category): array => $category->toArray(),
            $this->repository->findAll()
        );
    }

    /** @return array<string, mixed> */
    public function getCategoryById(int $id): array
    {
        $category = $this->findCategoryOrFail($id);
        return $category->toArray();
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createCategory(array $data): array
    {
        $name = trim((string) ($data['name'] ?? ''));
        $description = isset($data['description']) && trim((string) $data['description']) !== ''
            ? trim((string) $data['description'])
            : null;

        if ($name === '') {
            throw new ValidationException('Nama kategori wajib diisi');
        }

        $existing = $this->repository->findByName($name);
        if ($existing !== null) {
            throw new ConflictException('Nama kategori sudah terdaftar');
        }

        $created = $this->repository->create(new Category(null, $name, $description));
        return $created->toArray();
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateCategory(int $id, array $data): array
    {
        $current = $this->findCategoryOrFail($id);

        $name = array_key_exists('name', $data)
            ? trim((string) $data['name'])
            : $current->name;

        if ($name === '') {
            throw new ValidationException('Nama kategori wajib diisi');
        }

        $description = array_key_exists('description', $data)
            ? (isset($data['description']) && trim((string) $data['description']) !== ''
                ? trim((string) $data['description'])
                : null)
            : $current->description;

        $duplicate = $this->repository->findByName($name);
        if ($duplicate !== null && $duplicate->id !== $id) {
            throw new ConflictException('Nama kategori sudah digunakan oleh kategori lain');
        }

        $updated = $this->repository->update($id, new Category($id, $name, $description, $current->createdAt));
        if ($updated === null) {
            throw new NotFoundException("Kategori dengan ID {$id} tidak ditemukan");
        }

        return $updated->toArray();
    }

    public function deleteCategory(int $id): void
    {
        $this->findCategoryOrFail($id);

        if (!$this->repository->delete($id)) {
            throw new NotFoundException("Kategori dengan ID {$id} tidak ditemukan");
        }
    }

    private function findCategoryOrFail(int $id): Category
    {
        $category = $this->repository->findById($id);
        if ($category === null) {
            throw new NotFoundException("Kategori dengan ID {$id} tidak ditemukan");
        }

        return $category;
    }
}