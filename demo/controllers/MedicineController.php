<?php
require_once __DIR__ . '/../class/Medicine.php';


class MedicineController {
    private $medicineModel;

    public function __construct() {
        $this->medicineModel = new Medicine();
    }

    public function list() {
        return $this->medicineModel->getAll();
    }

    public function add($name, $stock, $image = null) {
        $name = trim($name);
        $stock = intval($stock);
        if ($name === '' || $stock < 0) {
            return false;
        }

        $imagePath = null;
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/';
            $fileName = uniqid() . '_' . basename($image['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($image['tmp_name'], $targetPath)) {
                $imagePath = 'uploads/' . $fileName;
            }
        }
        return $this->medicineModel->add($name, $stock, $imagePath);
    }

    public function getById(int $id) {
        return $this->medicineModel->getById($id);
    }

    public function update(int $id, $name, $stock, $image = null) {
        $name = trim($name);
        $stock = intval($stock);
        if ($name === '' || $stock < 0) {
            return false;
        }

        $imagePath = null;
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/';
            $fileName = uniqid() . '_' . basename($image['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($image['tmp_name'], $targetPath)) {
                $imagePath = 'uploads/' . $fileName;
            }
        }

        return $this->medicineModel->update($id, $name, $stock, $imagePath);
    }

    public function delete($id): bool {
        $id = intval($id);
        return $this->medicineModel->delete($id);
    }
}
?>
