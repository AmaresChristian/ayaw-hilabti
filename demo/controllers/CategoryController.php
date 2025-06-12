<?php
require_once __DIR__ . '/../class/Category.php';
require_once __DIR__ . '/../class/Database.php';

class CategoryController {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new Category(Database::getInstance());
    }

    public function list() {
        return $this->categoryModel->getAllCategories();
    }

    public function add($name) {
        $name = trim($name);
        if ($name === '') {
            return false;
        }
        return $this->categoryModel->addCategory($name);
    }

    public function getById(int $id) {
        return $this->categoryModel->getCategoryById($id);
    }

    public function update(int $id, $name) {
        $name = trim($name);
        if ($name === '') {
            return false;
        }
        return $this->categoryModel->updateCategory($id, $name);
    }

    public function delete(int $id): bool {
        return $this->categoryModel->deleteCategory($id);
    }
}