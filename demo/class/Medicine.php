<?php
require_once __DIR__ . '/Database.php';

class Medicine {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT m.*, c.name as category_name 
                                  FROM medicines m 
                                  LEFT JOIN categories c ON m.category_id = c.id 
                                  ORDER BY m.name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id) {
        $stmt = $this->pdo->prepare("SELECT m.*, c.name as category_name 
                                   FROM medicines m 
                                   LEFT JOIN categories c ON m.category_id = c.id 
                                   WHERE m.id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add(string $name, int $stock, string $imagePath = null, int $category_id = null) {
        $stmt = $this->pdo->prepare("INSERT INTO medicines (name, stock, image_path, category_id) 
                                VALUES (:name, :stock, :image_path, :category_id)");
        return $stmt->execute([
            'name' => $name,
            'stock' => $stock,
            'image_path' => $imagePath,
            'category_id' => $category_id
        ]);
    }

    public function update(int $id, string $name, int $stock, string $imagePath = null, int $category_id = null) {
        $stmt = $this->pdo->prepare("UPDATE medicines 
                                    SET name = :name, stock = :stock, image_path = :image_path, category_id = :category_id 
                                    WHERE id = :id");
        return $stmt->execute([
            'name' => $name,
            'stock' => $stock,
            'image_path' => $imagePath,
            'category_id' => $category_id,
            'id' => $id
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM medicines WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getCategories() {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
