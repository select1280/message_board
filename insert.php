<?php
session_start();
include 'db.php';

$name = trim($_POST['name'] ?? '');
$content = trim($_POST['content'] ?? '');
$captcha = trim($_POST['captcha'] ?? '');
$ip = $_SERVER['REMOTE_ADDR'];

// 驗證碼比對（不分大小寫）
if (strtoupper($captcha) !== strtoupper($_SESSION['captcha'] ?? '')) {
  echo "<div class='alert alert-danger'>驗證碼錯誤，請重新輸入。</div>";
  exit;
}

// 防灌水：10 秒內不能重複留言
$last = $_SESSION['last_post_time'] ?? 0;
if (time() - $last < 10) {
  echo "<div class='alert alert-warning'>請勿頻繁留言，請稍後再試。</div>";
  exit;
}
$_SESSION['last_post_time'] = time();

// 寫入留言
if ($name && $content) {
  $stmt = $pdo->prepare("INSERT INTO messages (name, content) VALUES (?, ?)");
  $stmt->execute([$name, $content]);
}

// 回傳最新留言清單
$stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
while ($row = $stmt->fetch()) {
  echo "<div class='card mb-2'>";
  echo "<div class='card-body'>";
  echo "<h5 class='card-title'>" . htmlspecialchars($row['name']) . "</h5>";
  echo "<p class='card-text'>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
  echo "<small class='text-muted'>{$row['created_at']}</small>";
  echo " <a href='delete.php?id={$row['id']}' class='btn btn-sm btn-danger float-end' onclick='return confirm(\"確定刪除？\")'>刪除</a>";
  echo "</div></div>";
}
