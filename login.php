<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if ($user === 'admin' && $pass === '1234') {
        $_SESSION['is_admin'] = true;
        header("Location: index.php");
        exit;
    } else {
        $error = "帳號或密碼錯誤";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>管理者登入</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
  <h2>管理者登入</h2>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label>帳號</label>
      <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>密碼</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">登入</button>
  </form>
</body>
</html>
