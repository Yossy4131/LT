# 関数・API仕様書

## 📋 概要

簡易SNSアプリケーションで使用される関数とAPI的な機能の詳細仕様書です。
各includesファイルの関数仕様と、フロントエンド機能について説明します。

## 🔧 includes/config.php

### データベース接続設定

#### `getDatabaseConnection()`

**概要**: PDOデータベース接続を取得

```php
function getDatabaseConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        $host = '192.168.33.10';
        $dbname = 'sns_db';
        $username = 'sns_user';
        $password = 'sns_pass';
        
        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, $username, $password, $options);
    }
    
    return $pdo;
}
```

**戻り値**: `PDO` - データベース接続オブジェクト
**例外**: `PDOException` - 接続失敗時

#### セッション設定

```php
// セキュアなセッション設定
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

session_start();
```

## 🔐 includes/auth.php

### 認証関連関数

#### `register_user($username, $display_name, $password)`

**概要**: 新規ユーザー登録処理

**パラメータ**:
- `$username` (string): ユーザー名（3-20文字、英数字+アンダースコア）
- `$display_name` (string): 表示名（1-50文字）
- `$password` (string): パスワード（8文字以上）

**戻り値**: 
- `true`: 登録成功
- `false`: 登録失敗

**処理フロー**:
```
入力値検証 → ユーザー名重複チェック → パスワードハッシュ化 → DB挿入
```

#### `authenticate_user($username, $password)`

**概要**: ユーザー認証処理

**パラメータ**:
- `$username` (string): ユーザー名
- `$password` (string): パスワード

**戻り値**:
- `array`: ユーザー情報（認証成功時）
- `false`: 認証失敗

**処理フロー**:
```
ユーザー存在確認 → パスワード検証 → ユーザー情報返却
```

#### `is_logged_in()`

**概要**: ログイン状態確認

**戻り値**: 
- `true`: ログイン済み
- `false`: 未ログイン

**使用例**:
```php
if (!is_logged_in()) {
    header('Location: pages/auth/login.php');
    exit();
}
```

#### `get_current_user()`

**概要**: 現在ログイン中のユーザー情報取得

**戻り値**:
- `array`: ユーザー情報
- `null`: 未ログイン

**返却データ**:
```php
[
    'id' => 1,
    'username' => 'admin',
    'display_name' => 'Administrator',
    'created_at' => '2025-07-12 10:00:00'
]
```

#### `logout_user()`

**概要**: ログアウト処理

**処理内容**:
- セッション変数クリア
- セッション破棄
- セッションクッキー削除

## 📝 includes/posts.php

### 投稿関連関数

#### `create_post($user_id, $content)`

**概要**: 新規投稿作成

**パラメータ**:
- `$user_id` (int): 投稿者のユーザーID
- `$content` (string): 投稿内容（最大1000文字）

**戻り値**:
- `int`: 作成された投稿のID（成功時）
- `false`: 作成失敗

**バリデーション**:
- 内容の空文字チェック
- 最大文字数チェック
- HTMLタグの除去

#### `get_recent_posts($limit = 50)`

**概要**: 最新投稿一覧取得

**パラメータ**:
- `$limit` (int): 取得件数（デフォルト: 50）

**戻り値**: `array` - 投稿一覧

**返却データ構造**:
```php
[
    [
        'id' => 1,
        'content' => '投稿内容',
        'username' => 'admin',
        'display_name' => 'Administrator',
        'created_at' => '2025-07-12 10:00:00'
    ],
    // ...
]
```

#### `get_user_posts($user_id, $limit = 50)`

**概要**: 特定ユーザーの投稿取得

**パラメータ**:
- `$user_id` (int): ユーザーID
- `$limit` (int): 取得件数（デフォルト: 50）

**戻り値**: `array` - 投稿一覧

#### `get_post_count($user_id)`

**概要**: ユーザーの投稿数取得

**パラメータ**:
- `$user_id` (int): ユーザーID

**戻り値**: `int` - 投稿数

## 🛡️ セキュリティ関数

### CSRF対策

#### `generate_csrf_token()`

**概要**: CSRFトークン生成

**戻り値**: `string` - CSRFトークン

**セッション保存**: `$_SESSION['csrf_token']`

#### `verify_csrf_token($token)`

**概要**: CSRFトークン検証

**パラメータ**:
- `$token` (string): 検証するトークン

**戻り値**:
- `true`: トークン有効
- `false`: トークン無効

### 入力値検証

#### `validate_username($username)`

**概要**: ユーザー名バリデーション

**パラメータ**:
- `$username` (string): 検証するユーザー名

**戻り値**:
- `true`: 有効
- `false`: 無効

**検証条件**:
- 3-20文字
- 英数字とアンダースコアのみ

#### `validate_password($password)`

**概要**: パスワードバリデーション

**パラメータ**:
- `$password` (string): 検証するパスワード

**戻り値**:
- `true`: 有効
- `false`: 無効

**検証条件**:
- 8文字以上

#### `escape_html($string)`

**概要**: HTMLエスケープ処理

**パラメータ**:
- `$string` (string): エスケープする文字列

**戻り値**: `string` - エスケープ後の文字列

**使用例**:
```php
echo escape_html($user_input);
```

## 📱 フロントエンド JavaScript API

### js/main.js

#### `updateCharacterCount()`

**概要**: 投稿フォームの文字数リアルタイム表示

**トリガー**: `input` イベント
**対象**: `#post-content` テキストエリア
**表示**: `#char-count` 要素

#### `updateRelativeTime()`

**概要**: 投稿日時の相対表示更新

**実行間隔**: 60秒
**対象**: `.relative-time` クラス要素
**表示形式**: 
- 1分未満: "たった今"
- 1-59分: "○分前"
- 1-23時間: "○時間前"
- 1日以上: "○日前"

#### `validatePostForm()`

**概要**: 投稿フォームのクライアントサイド検証

**検証項目**:
- 内容の空文字チェック
- 最大文字数チェック（1000文字）

**戻り値**:
- `true`: 検証通過
- `false`: 検証失敗

#### `showNotification(message, type)`

**概要**: 通知メッセージ表示

**パラメータ**:
- `message` (string): 表示メッセージ
- `type` (string): 'success', 'error', 'warning', 'info'

**表示時間**: 3秒（自動非表示）

## 🔄 API的なページ機能

### ページ機能仕様

#### `index.php`

**機能**: メインダッシュボード
**メソッド**: GET（表示）, POST（投稿作成）

**GET パラメータ**: なし
**POST パラメータ**:
- `content`: 投稿内容
- `csrf_token`: CSRFトークン

**レスポンス**: HTML（投稿一覧＋投稿フォーム）

#### `pages/auth/login.php`

**機能**: ユーザーログイン
**メソッド**: GET（表示）, POST（認証処理）

**POST パラメータ**:
- `username`: ユーザー名
- `password`: パスワード
- `csrf_token`: CSRFトークン

**成功時**: リダイレクト（`index.php`）
**失敗時**: エラーメッセージ表示

#### `pages/auth/register.php`

**機能**: ユーザー登録
**メソッド**: GET（表示）, POST（登録処理）

**POST パラメータ**:
- `username`: ユーザー名
- `display_name`: 表示名
- `password`: パスワード
- `password_confirm`: パスワード確認
- `csrf_token`: CSRFトークン

**成功時**: 自動ログイン＋リダイレクト
**失敗時**: エラーメッセージ表示

#### `pages/user/profile.php`

**機能**: ユーザープロフィール表示
**メソッド**: GET

**表示内容**:
- ユーザー基本情報
- 投稿数統計
- 自分の投稿一覧

## ⚡ パフォーマンス最適化

### データベース最適化

```sql
-- インデックス設定（実装済み）
CREATE INDEX idx_posts_created_at ON posts(created_at DESC);
CREATE INDEX idx_posts_user_id ON posts(user_id);
CREATE UNIQUE INDEX idx_users_username ON users(username);
```

### クエリ最適化

**N+1問題対策**: JOINを使用した一括取得
```php
// 投稿一覧取得時に投稿者情報も同時取得
$query = "
    SELECT p.*, u.username, u.display_name 
    FROM posts p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.created_at DESC 
    LIMIT ?
";
```

## 🧪 テスト仕様

### 単体テスト項目

1. **認証関数**
   - `authenticate_user()`: 正常系・異常系
   - `register_user()`: 重複チェック・バリデーション
   - `is_logged_in()`: セッション状態確認

2. **投稿関数**
   - `create_post()`: 正常投稿・文字数超過
   - `get_recent_posts()`: 取得件数・ソート順
   - `get_user_posts()`: ユーザー固有取得

3. **セキュリティ関数**
   - `escape_html()`: XSS対策確認
   - `verify_csrf_token()`: トークン検証
   - バリデーション関数群

### 結合テスト項目

1. **ユーザー登録 → ログイン → 投稿フロー**
2. **セッション管理**: タイムアウト・再ログイン
3. **エラーハンドリング**: DB接続エラー等

## 📊 ログ・モニタリング

### エラーログ形式

```php
// エラーログ出力例
error_log("SNS App Error: " . $error_message . " at " . __FILE__ . ":" . __LINE__);
```

### 監視対象指標

- **レスポンス時間**: 平均・最大
- **エラー率**: HTTP 4xx/5xx
- **DB接続状況**: 接続数・レスポンス時間
- **セッション数**: アクティブユーザー数

---

**文書バージョン**: 1.0  
**最終更新**: 2025年7月12日  
**作成者**: GitHub Copilot Assistant  
**技術レビュー**: 関数仕様の実装確認済み
