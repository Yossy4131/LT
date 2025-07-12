# 簡易SNS データベース設計書

## 概要

LT発表用簡易SNSアプリケーションのデータベース設計書です。
シンプルなSNS機能（ユーザー管理、投稿機能）を実現するためのテーブル構造を定義しています。

## データベース情報

- **データベース名**: `sns_db`
- **文字セット**: `utf8mb4`
- **照合順序**: `utf8mb4_unicode_ci`
- **DBMS**: MySQL 8.0
- **ストレージエンジン**: InnoDB

## テーブル一覧

| テーブル名 | 説明 | 主要な用途 |
|-----------|------|----------|
| users | ユーザー情報 | ユーザー登録、認証 |
| posts | 投稿情報 | 投稿作成、表示 |

## テーブル詳細

### 1. users テーブル

**テーブル説明**: ユーザーアカウント情報を管理

| カラム名 | データ型 | 制約 | デフォルト値 | 説明 |
|---------|---------|------|-------------|------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | - | ユーザーID（主キー） |
| username | VARCHAR(50) | UNIQUE, NOT NULL | - | ログイン用ユーザー名 |
| password_hash | VARCHAR(255) | NOT NULL | - | パスワードハッシュ（bcrypt） |
| display_name | VARCHAR(100) | NOT NULL | - | 表示名 |
| created_at | TIMESTAMP | NOT NULL | CURRENT_TIMESTAMP | アカウント作成日時 |

**インデックス**:
- PRIMARY KEY (id)
- UNIQUE KEY username (username)

**DDL**:
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**サンプルデータ**:
```sql
INSERT INTO users (username, password_hash, display_name) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator'),
('john_doe', '$2y$10$hash_example_1', 'John Doe'),
('jane_smith', '$2y$10$hash_example_2', 'Jane Smith');
```

### 2. posts テーブル

**テーブル説明**: ユーザーの投稿内容を管理

| カラム名 | データ型 | 制約 | デフォルト値 | 説明 |
|---------|---------|------|-------------|------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | - | 投稿ID（主キー） |
| user_id | INT | NOT NULL, FOREIGN KEY | - | 投稿者のユーザーID |
| content | TEXT | NOT NULL | - | 投稿内容（最大1000文字） |
| created_at | TIMESTAMP | NOT NULL | CURRENT_TIMESTAMP | 投稿日時 |

**インデックス**:
- PRIMARY KEY (id)
- KEY user_id (user_id)
- KEY created_at (created_at)

**外部キー制約**:
- FOREIGN KEY (user_id) REFERENCES users(id)

**DDL**:
```sql
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**サンプルデータ**:
```sql
INSERT INTO posts (user_id, content) VALUES
(1, 'Welcome to Simple SNS! This is the first post.'),
(2, 'Hello everyone! Nice to meet you.'),
(1, 'This is a demo application for LT presentation.'),
(3, 'Testing the SNS functionality...');
```

## ER図

```
┌─────────────────┐         ┌─────────────────┐
│     users       │         │     posts       │
├─────────────────┤         ├─────────────────┤
│ id (PK)         │◄────────│ id (PK)         │
│ username (UQ)   │         │ user_id (FK)    │
│ password_hash   │         │ content         │
│ display_name    │         │ created_at      │
│ created_at      │         └─────────────────┘
└─────────────────┘
```

## データ制約・ルール

### ビジネスルール

1. **ユーザー名**: 英数字とアンダースコアのみ、3-50文字
2. **パスワード**: 最小6文字以上
3. **表示名**: 最大100文字
4. **投稿内容**: 最大1000文字
5. **ユーザー削除**: 関連する投稿も削除される（CASCADE）

### データ整合性

- ユーザー名は一意である必要がある
- 投稿は必ず存在するユーザーに関連付けられる
- パスワードは平文保存せず、bcryptでハッシュ化

## セキュリティ考慮事項

### パスワード管理
- bcryptアルゴリズムによるハッシュ化
- ソルト自動生成
- レインボーテーブル攻撃対策

### SQLインジェクション対策
- PDO prepared statementsの使用
- ユーザー入力値の適切なバインド

### XSS対策
- 出力時のHTMLエスケープ
- CSRFトークンによる偽造リクエスト対策

## パフォーマンス最適化

### インデックス設計

1. **users.username**: ログイン時の高速検索
2. **posts.user_id**: ユーザー別投稿取得の最適化
3. **posts.created_at**: 時系列ソートの最適化

### クエリ最適化例

```sql
-- 最新投稿取得（インデックス使用）
SELECT p.*, u.display_name, u.username
FROM posts p
JOIN users u ON p.user_id = u.id
ORDER BY p.created_at DESC
LIMIT 20;

-- ユーザー別投稿取得（インデックス使用）
SELECT p.*, u.display_name, u.username
FROM posts p
JOIN users u ON p.user_id = u.id
WHERE p.user_id = ?
ORDER BY p.created_at DESC;
```

## 将来の拡張可能性

### Phase 2 拡張候補

1. **フォロー機能**
```sql
CREATE TABLE follows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_id INT NOT NULL,
    followed_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (follower_id) REFERENCES users(id),
    FOREIGN KEY (followed_id) REFERENCES users(id),
    UNIQUE KEY unique_follow (follower_id, followed_id)
);
```

2. **いいね機能**
```sql
CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (post_id) REFERENCES posts(id),
    UNIQUE KEY unique_like (user_id, post_id)
);
```

3. **コメント機能**
```sql
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

4. **メディアファイル**
```sql
CREATE TABLE media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type ENUM('image', 'video', 'document') NOT NULL,
    file_size INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id)
);
```

## バックアップ・復旧

### 推奨バックアップ戦略

1. **日次フルバックアップ**
```bash
mysqldump -u root -p sns_db > backup_$(date +%Y%m%d).sql
```

2. **バイナリログバックアップ**（増分）
```bash
mysqladmin flush-logs
```

3. **復旧手順**
```bash
mysql -u root -p sns_db < backup_20250712.sql
```

## 運用・監視

### 重要な監視項目

1. **テーブルサイズ監視**
```sql
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.tables 
WHERE table_schema = 'sns_db';
```

2. **ユーザー増加監視**
```sql
SELECT 
    DATE(created_at) as date,
    COUNT(*) as new_users
FROM users 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(created_at)
ORDER BY date;
```

3. **投稿数監視**
```sql
SELECT 
    DATE(created_at) as date,
    COUNT(*) as new_posts
FROM posts 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(created_at)
ORDER BY date;
```

---

**作成者**: GitHub Copilot Assistant  
**作成日**: 2025年7月12日  
**バージョン**: 1.0  
**用途**: LT発表用デモアプリケーション
