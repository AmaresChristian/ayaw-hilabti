<?php
require_once __DIR__ . '/../controllers/AuthController.php';

// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug: Log session status
error_log('Session status: ' . session_status());

$auth = new AuthController();

// Debug: Check if already logged in
if ($auth->check()) {
    error_log('User already logged in: ' . print_r($_SESSION['user'], true));
    $role = $auth->user()['role'];
    header("Location: /demo/public/$role/dashboard.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = htmlspecialchars($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Debug: Log received credentials (remove in production)
error_log('Login attempt - Username: ' . $username . ', Password: ' . $password);

        if (empty($username) || empty($password)) {
            throw new Exception('Username and password are required');
        }
        
        if ($auth->login($username, $password)) {
            error_log('Login successful for user: ' . $username);
            $role = $auth->user()['role'];
            $redirectUrl = "/demo/public/$role/dashboard.php";
            error_log('Redirecting to: ' . $redirectUrl);
            header("Location: $redirectUrl");
            exit();
        } else {
            error_log('Login failed for user: ' . $username);
            throw new Exception('Invalid username or password');
        }
    } catch (Exception $e) {
        error_log('Login error: ' . $e->getMessage());
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Pharmacy Inventory</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
  <style>
    body {
      display: flex;
      min-height: 100vh;
      justify-content: center;
      align-items: center;
      background:rgb(238, 182, 0);
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0 12px;
    }
    .login-container {
      background: white;
      padding: 32px 28px;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      max-width: 400px;
      width: 100%;
      box-sizing: border-box;
      text-align: center;
    }
    h1 {
      margin-bottom: 24px;
      color: #0369a1;
      font-weight: 900;
      font-size: 2rem;
      background: linear-gradient(135deg, #2563eb, #06b6d4);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 18px;
    }
    input[type="text"],
    input[type="password"] {
      padding: 14px 18px;
      font-size: 1rem;
      border-radius: 12px;
      border: 1.5px solid #cbd5e1;
      outline-offset: 2px;
      transition: border-color 0.3s ease;
      box-sizing: border-box;
    }
    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #06b6d4;
      background-color: #ffffff;
    }
    button {
      background: linear-gradient(135deg, #2563eb, #06b6d4);
      border: none;
      color: white;
      padding: 14px 24px;
      font-weight: 700;
      font-size: 1.1rem;
      border-radius: 12px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    button:hover,
    button:focus-visible {
      background: linear-gradient(135deg, #1e40af, #0891b2);
      outline: 3px solid #2563eb;
    }
    .error {
      color: #dc2626;
      font-weight: 600;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h1>Pharmacy Inventory</h1>
    <?php if ($error): ?>
      <div class="error" role="alert"><?=htmlspecialchars($error)?></div>
    <?php endif; ?>
    <form method="post">
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
