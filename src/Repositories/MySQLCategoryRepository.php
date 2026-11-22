<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\CategoryRepositoryInterface;
use App\Exceptions\DatabaseException;
use App\Models\Category;
use PDO;
use PDOException;

class MySQLCategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(private readonly PDO $pdo) {}

    public function findAll(): array
    {
        try {
            $stmt =$this->pdo->query('SELECT id, name, description, created_at FROM categories ORDER BY id ASC');
            $rows =$stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(fn(array $row): Category =>$this->mapRow($row),$rows);
        } catch (PDOException $e) {
            throw new DatabaseException('Gagal mengambil data kategori: ' . $e->getMessage(), 0,$e);
        }
    }

    public function findById(int $id): ?Category
    {
        try {
            $stmt =$this->pdo->prepare('SELECT id, name, description, created_at FROM categories WHERE id = :id LIMIT 1');
            $stmt->execute(['id' =>$id]);
            $row =$stmt->fetch(PDO::FETCH_ASSOC);

            return $row ? $this->mapRow($row) : null;
        } catch (PDOException $e) {
            throw new DatabaseException('Gagal mencari kategori berdasarkan ID: ' . $e->getMessage(), 0,$e);
        }
    }

    public function findByName(string $name): ?Category
    {
        try {
            $stmt =$this->pdo->prepare('SELECT id, name, description, created_at FROM categories WHERE LOWER(name) = LOWER(:name) LIMIT 1');
            $stmt->execute(['name' =>$name]);
            $row =$stmt->fetch(PDO::FETCH_ASSOC);

            return $row ? $this->mapRow($row) : null;
        } catch (PDOException $e) {
            throw new DatabaseException('Gagal mencari kategori berdasarkan nama: ' . $e->getMessage(), 0,$e);
        }
    }

    public function create(Category $category): Category
    {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO categories (name, description) VALUES (:name, :description)');$stmt->execute([
                'name' => $category->name,
                'description' => $category->description,
            ]);

            $id = (int)$this->pdo->lastInsertId();
            return $this->findById($id) ?? new Category($id, $category->name,$category->description);
        } catch (PDOException $e) {
            throw new DatabaseException('Gagal menambah kategori: ' . $e->getMessage(), 0,$e);
        }
    }

    public function update(int $id, Category$category): ?Category
    {
        try {
            $stmt = $this->pdo->prepare('UPDATE categories SET name = :name, description = :description WHERE id = :id');$stmt->execute([
                'id' => $id,
                'name' => $category->name,
                'description' => $category->description,
            ]);

            return $this->findById($id);
        } catch (PDOException $e) {
            throw new DatabaseException('Gagal memperbarui kategori: ' . $e->getMessage(), 0,$e);
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt =$this->pdo->prepare('DELETE FROM categories WHERE id = :id');
            $stmt->execute(['id' =>$id]);

            return $stmt->rowCount() > 0; } catch (PDOException$e) {
            throw new DatabaseException('Gagal menghapus kategori: ' . $e->getMessage(), 0,$e);
        }
    }

    private function mapRow(array $row): Category
    {
        return new Category(
            id: isset($row['id']) ? (int)$row['id'] : null,
            name: (string) $row['name'],
            description: isset($row['description']) ? (string)$row['description'] : null,
            createdAt: isset($row['created_at']) ? (string)$row['created_at'] : null
        );
    }
}