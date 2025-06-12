<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../controllers/MedicineController.php';

$auth = new AuthController();
$auth->requireLogin();
$auth->requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $medicineCtrl = new MedicineController();
    if ($medicineCtrl->delete(intval($_POST['id']))) {
        header('Location: dashboard.php?message=Deleted+successfully');
    } else {
        header('Location: dashboard.php?error=Failed+to+delete');
    }
    exit;
}

header('Location: dashboard.php');