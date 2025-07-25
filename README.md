# PHP 留言板系統（Message Board）

這是一個使用 PHP 與 MySQL 開發的進階留言板系統，具備即時留言、子留言回覆、圖形驗證碼防灌水、留言搜尋與排序、Bootstrap 美化、管理者登入與留言刪除功能。

---

##  專案功能

-  使用者可輸入姓名與留言，留言即時顯示（AJAX 無刷新）
-  每則主留言支援子留言回覆
-  主留言具備分頁顯示（每頁 5 筆）
-  支援留言關鍵字搜尋
-  可切換留言排序：最新優先 / 最舊優先
-  圖形驗證碼防止機器人洗留言
-  防灌水機制（每 10 秒僅能留言一次）
-  管理者登入後可刪除留言
-  使用 Bootstrap 打造現代響應式 UI

---

##  使用技術

| 類別         | 技術說明                             |
|--------------|--------------------------------------|
| 後端語言     | PHP 8（原生 PHP 開發）               |
| 資料庫       | MySQL（留言資料 + 子留言結構）       |
| 前端介面     | HTML5 + Bootstrap 5                  |
| 前端互動     | jQuery + AJAX（即時無刷新送出留言） |
| 安全防護     | CAPTCHA 驗證碼 + Session 防灌水控制  |
| 權限控制     | 管理者登入 / 登出（PHP Session）     |

---

##  資料表結構

```sql
CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  content TEXT NOT NULL,
  parent_id INT DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
