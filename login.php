<?php
session_start();
require_once("db.php");

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->execute([$username]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user'] = $user['username'];
    header("Location: index.php");
    exit;
  } else {
    $error = "Invalid username or password.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Diary App</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f2f4f8;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .login-container {
      background: #ffffff;
      padding: 30px 35px;
      width: 100%;
      max-width: 400px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    .login-container h2 {
      margin-bottom: 25px;
      text-align: center;
      font-size: 24px;
      color: #333;
    }

    .form-group {
      margin-bottom: 16px;
    }

    .form-group label {
      display: block;
      font-size: 14px;
      margin-bottom: 5px;
      color: #444;
    }

    .form-group input {
      width: 100%;
      padding: 10px;
      font-size: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      transition: border 0.3s;
    }

    .form-group input:focus {
      border-color: #007bff;
      outline: none;
    }

    .error-message {
      color: #dc3545;
      font-size: 14px;
      margin-bottom: 15px;
      text-align: center;
    }

    .btn-login {
      width: 100%;
      padding: 12px;
      background-color: #007bff;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      color: white;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn-login:hover {
      background-color: #0056b3;
    }

    @media screen and (max-width: 480px) {
      .login-container {
        margin: 0 15px;
        padding: 25px 20px;
      }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Login</h2>
    <?php if ($error): ?>
      <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="form-group">
        <label for="username">Email / Username</label>
        <input type="text" id="username" name="username" required placeholder="Enter your username" />
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required placeholder="Enter your password" />
      </div>
      <button class="btn-login" type="submit">Login</button>
    </form>
  </div>
</body>
</html>
