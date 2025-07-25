# PHP 留言板系統（Message Board）

這是一個使用 PHP 與 MySQL 開發的留言板系統，支援即時留言、驗證碼防灌水、Bootstrap 美化與管理者登入刪除功能。

---

## 專案功能

- 使用者可輸入姓名與留言
- AJAX 無刷新方式送出留言
- 圖形驗證碼機制防止機器人洗留言
- 防灌水機制（10 秒內不可重複留言）
- 管理者登入與刪除留言功能
- Bootstrap 介面設計

---

## 使用技術

| 類別        | 技術說明                    |
|-------------|-----------------------------|
| 語言        | PHP 8, HTML5, JavaScript    |
| 資料庫      | MySQL                       |
| UI 框架     | Bootstrap 5                 |
| 前端互動    | jQuery + AJAX               |
| 驗證與安全  | CAPTCHA 驗證碼 + Session 防灌水 |
| 管理權限    | 管理者登入（PHP Session）   |

---

## 資料表結構（MySQL）

```sql
CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
