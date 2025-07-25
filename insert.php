<?php
include 'db.php';

$name = $_POST['name'] ?? '';
$content = $_POST['content'] ?? '';

if ($name && $content) {
  $stmt = $pdo->prepare("INSERT INTO messages (name, content) VALUES (?, ?)");
  $stmt->execute([$name, $content]);
}

// 回傳最新留言清單（卡片格式）
$stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
while ($row = $stmt->fetch()) {
  echo "<div class='card mb-2'>";
  echo "<div class='card-body'>";
  echo "<h5 class='card-title'>{$row['name']}</h5>";
  echo "<p class='card-text'>{$row['content']}</p>";
  echo "<small class='text-muted'>{$row['created_at']}</small>";
  echo " <a href='delete.php?id={$row['id']}' class='btn btn-sm btn-danger float-end'>刪除</a>";
  echo "</div></div>";
}
