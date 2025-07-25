<?php require_once 'db.php';
session_start();
include 'db.php';
$isAdmin = $_SESSION['is_admin'] ?? false;
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>留言板</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container py-5">
    <h1 class="mb-4">留言板</h1>
    <!-- 在 HTML 中加上登入狀態提示與登出按鈕 -->
    <div class="mb-4 text-end">
        <?php if ($isAdmin): ?>
            <p>已登入管理者 | <a href="logout.php" class="btn btn-sm btn-outline-secondary">登出</a></p>
        <?php else: ?>
            <p><a href="login.php" class="btn btn-sm btn-outline-primary">管理者登入</a></p>
        <?php endif; ?>
    </div>

    <form id="messageForm" method="post" class="mb-4">
        </div>
        <div class="mb-3">
            <label>姓名</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>留言</label>
            <textarea name="content" class="form-control" required></textarea>
        </div>
        <label>驗證碼</label>
        <div class="d-flex align-items-center">
            <input type="text" name="captcha" class="form-control me-2" required>
            <img id="captchaImg" src="captcha.php" onclick="this.src='captcha.php?'+Math.random()" style="cursor:pointer;" title="點擊重新產生">
        </div>
        <button type="submit" class="btn btn-primary">送出留言</button>
    </form>

    <div id="messages">
        <?php
        $stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
        while ($row = $stmt->fetch()):
        ?>
            <div class='card mb-2'>
                <div class='card-body'>
                    <h5 class='card-title'><?= htmlspecialchars($row['name']) ?></h5>
                    <p class='card-text'><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                    <small class='text-muted'><?= $row['created_at'] ?></small>

                    <?php if ($isAdmin): ?>
                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger float-end" onclick="return confirm('確定刪除？')">刪除</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#messageForm').on('submit', function(e) {
            e.preventDefault();

            $.post('insert.php', $(this).serialize(), function(data) {
                if (data.includes('驗證碼錯誤') || data.includes('請勿頻繁留言')) {
                    // 若有錯誤，保留輸入但仍刷新驗證碼
                    $('#captchaImg').attr('src', 'captcha.php?' + Math.random());
                    alert($(data).text()); // 簡單 alert 提示錯誤（或可自定區塊顯示）
                    return;
                }

                $('#messages').html(data); // 更新留言區
                $('#messageForm')[0].reset(); // 清空表單
                $('#captchaImg').attr('src', 'captcha.php?' + Math.random()); //刷新驗證碼
            });
        });
    </script>

</body>

</html>