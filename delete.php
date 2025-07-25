<?php
include 'db.php';

$id = $_GET['id'] ?? '';

if ($id) {
  $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
  $stmt->execute([$id]);
}

header("Location: index.php");
