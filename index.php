<?php
session_start();
require_once 'db.php';

$isAdmin = $_SESSION['is_admin'] ?? false;
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>留言板</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="container py-5">
    <h1 class="mb-4">留言板</h1>
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="form-control" placeholder="搜尋留言關鍵字...">
        </div>
        <div class="col-md-4">
            <select name="sort" class="form-select">
                <option value="desc" <?= ($_GET['sort'] ?? '') !== 'asc' ? 'selected' : '' ?>>最新留言優先</option>
                <option value="asc" <?= ($_GET['sort'] ?? '') === 'asc' ? 'selected' : '' ?>>最舊留言優先</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100" type="submit">搜尋</button>
        </div>
        <div class="col-md-2">
            <a href="index.php" class="btn btn-secondary w-100">重設</a>
        </div>
    </form>


    <!-- 管理者登入狀態 -->
    <div class="mb-4 text-end">
        <?php if ($isAdmin): ?>
            <p>已登入管理者 | <a href="logout.php" class="btn btn-sm btn-outline-secondary">登出</a></p>
        <?php else: ?>
            <p><a href="login.php" class="btn btn-sm btn-outline-primary">管理者登入</a></p>
        <?php endif; ?>
    </div>

    <!-- 主留言表單 -->
    <form id="messageForm" method="post" class="mb-4">
        <div class="mb-3">
            <label>姓名</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>留言</label>
            <textarea name="content" class="form-control" required></textarea>
        </div>
        <label>驗證碼</label>
        <div class="d-flex align-items-center mb-3">
            <input type="text" name="captcha" class="form-control me-2" required>
            <img id="captchaImg" src="captcha.php" onclick="this.src='captcha.php?'+Math.random()" style="cursor:pointer;" title="點擊重新產生">
        </div>
        <button type="submit" class="btn btn-primary">送出留言</button>
    </form>

    <!-- 留言顯示區 -->
    <div id="messages">
        <?php
        // 分頁查詢主留言（parent_id IS NULL）
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 5;
        $offset = ($page - 1) * $perPage;

        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'desc';
        $orderBy = ($sort === 'asc') ? 'ASC' : 'DESC';

        $searchSql = '';
        $params = [];

        if ($search) {
            $searchSql = ' AND content LIKE :keyword';
            $params[':keyword'] = '%' . $search . '%';
        }

        // 查詢留言（主留言）
        $sql = "SELECT * FROM messages WHERE parent_id IS NULL $searchSql ORDER BY created_at $orderBy LIMIT :offset, :perPage";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->execute();

        // 查詢總筆數（for 分頁）
        $sqlCount = "SELECT COUNT(*) FROM messages WHERE parent_id IS NULL $searchSql";
        $stmtCount = $pdo->prepare($sqlCount);
        foreach ($params as $key => $val) {
            $stmtCount->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmtCount->execute();
        $total = $stmtCount->fetchColumn();
        $totalPages = ceil($total / $perPage);


        while ($row = $stmt->fetch()):
            $id = $row['id'];
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

                <!-- 回覆表單 -->
                <div class="card-body border-top bg-light">
                    <form method="post" action="insert.php">
                        <input type="hidden" name="name" value="管理者回覆">
                        <input type="hidden" name="parent_id" value="<?= $id ?>">
                        <div class="input-group">
                            <input type="text" name="content" class="form-control" placeholder="回覆內容..." required>
                            <button class="btn btn-sm btn-outline-primary" type="submit">送出</button>
                        </div>
                    </form>
                </div>

                <!-- 顯示子留言 -->
                <?php
                $replies = $pdo->prepare("SELECT * FROM messages WHERE parent_id = ? ORDER BY created_at ASC");
                $replies->execute([$id]);
                while ($reply = $replies->fetch()):
                ?>
                    <div class="card-body ms-4 border-start border-2">
                        <strong><?= htmlspecialchars($reply['name']) ?></strong>：
                        <?= nl2br(htmlspecialchars($reply['content'])) ?>
                        <div><small class="text-muted"><?= $reply['created_at'] ?></small></div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endwhile; ?>

        <!-- 分頁按鈕 -->
        <nav>
            <ul class="pagination justify-content-center mt-4">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <!-- 主留言 AJAX 無刷新提交 -->
    <script>
        $('#messageForm').on('submit', function(e) {
            e.preventDefault();

            $.post('insert.php', $(this).serialize(), function(data) {
                if (data.includes('驗證碼錯誤') || data.includes('請勿頻繁留言')) {
                    $('#captchaImg').attr('src', 'captcha.php?' + Math.random());
                    alert($(data).text());
                    return;
                }

                $('#messages').html(data);
                $('#messageForm')[0].reset();
                $('#captchaImg').attr('src', 'captcha.php?' + Math.random());
            });
        });
    </script>
</body>

</html>