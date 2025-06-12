<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../controllers/MedicineController.php';
require_once __DIR__ . '/../../controllers/CategoryController.php';

$auth = new AuthController();
$auth->requireLogin();
$auth->requireRole('admin');

$medicineCtrl = new MedicineController();

// Handle POST requests for add/edit/update
$errors = [];
$messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'] ?? '';
        $stock = $_POST['stock'] ?? '';
        if (!$medicineCtrl->add($name, $stock, $_FILES['image'] ?? null)) {
            $errors[] = "Failed to add medicine. Ensure name isn't empty and stock is non-negative integer.";
        } else {
            $messages[] = "Medicine added successfully.";
        }
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $stock = $_POST['stock'] ?? '';
        if (!$medicineCtrl->update($id, $name, $stock)) {
            $errors[] = "Failed to update medicine. Ensure valid inputs.";
        } else {
            $messages[] = "Medicine updated successfully.";
        }
    }
}
$medicines = $medicineCtrl->list();
$user = $auth->user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard - Pharmacy Inventory</title>
<link rel="stylesheet" href="/assets/css/style.css" />
<style>
  body {
    font-family: 'Inter', sans-serif;
    background:rgb(255, 255, 255);
    margin: 0;
    padding: 0 12px 40px;
    min-height: 100vh;
  }
  header {
    padding: 20px 0;
    text-align: center;
    background: linear-gradient(135deg, rgb(243, 146, 1), rgb(243, 116, 1));
    color: white;
    font-weight: 700;
    border-radius: 0 0 16px 16px;
  }
  main {
    max-width: 900px;
    margin: 32px auto;
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
  }
  h2 {
    font-size: 1.6rem;
    margin-bottom: 24px;
    color: #0369a1;
  }
  .messages {
    margin-bottom: 20px;
  }
  .messages .error {
    background: #fee2e2;
    color: #b91c1c;
    padding: 14px 18px;
    border-radius: 12px;
    font-weight: 600;
    margin-bottom: 8px;
  }
  .messages .success {
    background: #dcfce7;
    color: #15803d;
    padding: 14px 18px;
    border-radius: 12px;
    font-weight: 600;
    margin-bottom: 8px;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 40px;
  }
  th, td {
    border-bottom: 1px solid #e0e7ff;
    padding: 16px 12px;
    text-align: left;
  }
  th {
    background: #f0f9ff;
    font-weight: 700;
  }
  form input[type="text"],
  form input[type="number"] {
    width: 100%;
    padding: 8px 12px;
    border-radius: 12px;
    border: 1.5px solid #cbd5e1;
    font-size: 1rem;
  }
  form button {
    margin-top: 8px;
    background: linear-gradient(135deg, #2563eb, #06b6d4);
    color: white;
    border: none;
    font-weight: 700;
    padding: 10px 24px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  form button:hover,
  form button:focus-visible {
    background: linear-gradient(135deg, #1e40af, #0891b2);
    outline: 3px solid #2563eb;
  }
  .logout-button {
    background: #dc2626;
    margin-top: 12px;
    border-radius: 12px;
    font-weight: 700;
    padding: 10px 20px;
    cursor: pointer;
  }
  .logout-button:hover,
  .logout-button:focus-visible {
    background: #991b1b;
    outline: 3px solid #dc2626;
  }
</style>
</head>
<body>
<header>
  <h1>Welcome, <?=htmlspecialchars($user['username'])?></h1>
</header>

<main>
  <section aria-labelledby="addMedicineTitle">
  <h2 id="addMedicineTitle">Add New Item</h2>
    <form method="post" enctype="multipart/form-data" novalidate style="display: grid; gap: 20px; max-width: 500px; margin-bottom: 30px;">
        <input type="hidden" name="add" value="1" />
        <div class="form-group" style="display: grid; gap: 8px;">
            <label for="item-name">Item Name:</label>
            <input type="text" id="item-name" name="name" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" />
        </div>
        <div class="form-group" style="display: grid; gap: 8px;">
            <label for="item-name">Category:</label>
            <input type="text" id="item-name" name="name" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" />
        </div>
        <div class="form-group" style="display: grid; gap: 8px;">
            <label for="item-image">Item Image:</label>
            <input type="file" id="item-image" name="image" accept="image/*" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background: white;" />
        </div>
        <div class="form-group" style="display: grid; gap: 8px;">
            <label for="item-stock">Stock Quantity:</label>
            <input type="number" id="item-stock" name="stock" min="0" value="0" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" />
        </div>
        <button type="submit" style="background: #0ea5e9; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; justify-self: start;">Add Item</button>
    </form>
</section>

  <section aria-labelledby="medicinesTitle" style="margin-top: 48px;">
    <h2 id="medicinesTitle">Inventory</h2>
    <div class="messages">
      <?php foreach ($errors as $error): ?>
        <div class="error" role="alert"><?=htmlspecialchars($error)?></div>
      <?php endforeach; ?>
      <?php foreach ($messages as $msg): ?>
        <div class="success"><?=htmlspecialchars($msg)?></div>
      <?php endforeach; ?>
    </div>
    <?php if (count($medicines) === 0): ?>
      <p>No medicines found.</p>
    <?php else: ?>
      <table aria-describedby="inventoryDesc">
        <caption id="inventoryDesc" class="sr-only">List of medicines with current stock and update option</caption>
        <thead>
          <tr>
            <th scope="col">Image</th>
            <th scope="col">Medicine Name</th>
            <th scope="col" style="width: 150px;">Stock</th>
            <th scope="col" style="width: 280px;">Update</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($medicines as $med): ?>
          <tr>
            <td style="width: 100px; padding: 8px;">
              <?php if (!empty($med['image_path'])): ?>
                <img src="/demo/<?=htmlspecialchars($med['image_path'])?>" alt="<?=htmlspecialchars($med['name'])?>" style="max-width: 80px; height: auto;" />
              <?php else: ?>
                <div style="width: 80px; height: 80px; background: #f3f4f6; display: flex; align-items: center; justify-content: center;">No image</div>
              <?php endif; ?>
            </td>
            <td><?=htmlspecialchars($med['name'])?></td>
            <td><?=intval($med['stock'])?></td>
            <td>
              <form method="post" enctype="multipart/form-data" style="display:flex; gap:12px; align-items:center;">
                <input type="hidden" name="edit" value="1" />
                <input type="hidden" name="id" value="<?=intval($med['id'])?>" />
                <input name="name" type="text" value="<?=htmlspecialchars($med['name'])?>" required style="width:140px;" />
                <input name="stock" type="number" min="0" value="<?=intval($med['stock'])?>" required style="width:100px;" />
                <input type="file" name="image" accept="image/*" style="width:140px;" />
                <button type="submit" aria-label="Update <?=htmlspecialchars($med['name'])?>">Update</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

  
  <button type="submit" class="logout-button" aria-label="Logout"><a href="/demo/public/admin/logout.php">Logout</button>
    
  </form>
</main>
</body>
</html>





    <form method="post" action="delete.php" style="display:inline;">
        <input type="hidden" name="id" value="<?=$med['id']?>" />
        <button type="submit" class="delete-button">Delete</button>
    </form>
</td>
