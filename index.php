<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>留言板</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container py-5">
    <h1 class="mb-4">留言板</h1>

    <form id="messageForm" method="post" class="mb-4">
        <div class="mb-3">
            <label>姓名</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>留言</label>
            <textarea name="content" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">送出留言</button>
    </form>

    <div id="messages">
        <?php
        $stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
        while ($row = $stmt->fetch()) {
            echo "<div class='card mb-2'>";
            echo "<div class='card-body'>";
            echo "<h5 class='card-title'>{$row['name']}</h5>";
            echo "<p class='card-text'>{$row['content']}</p>";
            echo "<small class='text-muted'>{$row['created_at']}</small>";
            echo " <a href='delete.php?id={$row['id']}' class='btn btn-sm btn-danger float-end' onclick='return confirm(\"確定刪除？\")'>刪除</a>";
            echo "</div></div>";
        }
        ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#messageForm').on('submit', function(e) {
            e.preventDefault();
            $.post('insert.php', $(this).serialize(), function(data) {
                $('#messages').html(data); // 回傳整段留言列表
                $('#messageForm')[0].reset(); // 清空表單
            });
        });
    </script>

</body>

</html>