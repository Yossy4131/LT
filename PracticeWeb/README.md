# PracticeWeb - 簡易SNS Webアプリケーション

LT発表用の簡易SNSウェブアプリケーションのソースコードです。

## 📁 ディレクトリ構成

```
PracticeWeb/
├── index.php                    # メインページ（投稿一覧・投稿フォーム）
├── login.php                    # リダイレクト用（下位互換）
├── register.php                 # リダイレクト用（下位互換）
├── logout.php                   # リダイレクト用（下位互換）
├── profile.php                  # リダイレクト用（下位互換）
├── .htaccess                    # Apache設定ファイル（参考用）
├── pages/                       # ページファイル
│   ├── auth/                    # 認証関連ページ
│   │   ├── login.php           # ログインページ
│   │   ├── register.php        # ユーザー登録ページ
│   │   └── logout.php          # ログアウト処理
│   └── user/                    # ユーザー関連ページ
│       └── profile.php         # プロフィールページ
├── includes/                    # 共通PHPファイル
│   ├── config.php              # 設定・DB接続
│   ├── auth.php                # 認証関連関数
│   └── posts.php               # 投稿関連関数
├── css/                        # スタイルシート
│   └── style.css               # メインCSS
├── js/                         # JavaScript
│   └── main.js                 # メインJavaScript
├── docs/                       # ドキュメント
│   ├── database_design.md      # データベース設計書
│   ├── application_spec.md     # アプリケーション仕様書
│   ├── security.md             # セキュリティ設計書
│   └── functions.md            # 関数・API仕様書
    └── database_design.md      # データベース設計書
```

## 🎯 主な機能

### 認証機能
- **ユーザー登録**: 新規アカウント作成
- **ログイン**: セッション管理によるログイン
- **ログアウト**: セッション削除
- **セキュリティ**: CSRF対策、XSS対策、SQLインジェクション対策

### 投稿機能
- **投稿作成**: テキスト投稿（最大1000文字）
- **投稿一覧**: 全ユーザーの投稿を新しい順で表示
- **投稿表示**: ユーザー情報と投稿日時を含む表示

### ユーザー機能
- **プロフィール表示**: ユーザー情報と投稿数の表示
- **個人投稿一覧**: 自分の投稿のみを表示

### UI/UX
- **レスポンシブデザイン**: スマートフォン対応
- **リアルタイム機能**: 文字数カウンター、フォームバリデーション
- **モダンなデザイン**: CSS3を活用したUIデザイン

## 🔧 技術スタック

### バックエンド
- **PHP 8.1**: サーバーサイド言語
- **MySQL 8.0**: データベース
- **PDO**: データベース接続

### フロントエンド
- **HTML5**: マークアップ
- **CSS3**: スタイリング（Flexbox、Grid）
- **JavaScript ES6+**: クライアントサイド機能

### セキュリティ
- **bcrypt**: パスワードハッシュ化
- **PDO Prepared Statements**: SQLインジェクション対策
- **htmlspecialchars**: XSS対策
- **CSRFトークン**: CSRF攻撃対策

## 🌐 ページ構成

### 認証関連ページ

#### `/pages/auth/login.php`
- ユーザー名とパスワードによるログイン
- セッション管理
- エラーハンドリング

#### `/pages/auth/register.php`
- 新規ユーザー登録
- バリデーション機能
- パスワード確認

#### `/pages/auth/logout.php`
- セッション削除
- ログインページへリダイレクト

### ユーザー関連ページ

#### `/pages/user/profile.php`
- ユーザー情報表示
- 個人投稿一覧
- 投稿数統計

### メインページ

#### `/index.php`
- 投稿作成フォーム
- 全体投稿一覧
- リアルタイム機能

## 🔒 セキュリティ対策

### 実装済み対策

1. **SQLインジェクション対策**
   - PDO prepared statements使用
   - ユーザー入力の適切なバインド

2. **XSS（クロスサイトスクリプティング）対策**
   - 出力時のHTMLエスケープ（`htmlspecialchars`関数）
   - 入力値の検証

3. **CSRF（クロスサイトリクエストフォージェリ）対策**
   - CSRFトークンの生成と検証
   - セッションベースのトークン管理

4. **パスワードセキュリティ**
   - bcryptによるハッシュ化
   - ソルト自動生成
   - レインボーテーブル攻撃対策

5. **セッション管理**
   - セキュアなセッション設定
   - ログイン状態の適切な管理

### セキュリティ設定例

```php
// XSS対策
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// CSRF対策
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// SQLインジェクション対策
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
```

## 📱 レスポンシブデザイン

### ブレークポイント
- **デスクトップ**: 769px以上
- **タブレット**: 768px以下
- **スマートフォン**: 480px以下

### 対応機能
- フレキシブルレイアウト
- タッチフレンドリーなUI
- 読みやすいフォントサイズ

## 🚀 セットアップ手順

### 1. 前提条件
- Webサーバー（Nginx/Apache）
- PHP 8.1以上
- MySQL 8.0以上

### 2. データベース設定
```sql
-- データベース作成
CREATE DATABASE sns_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- テーブル作成は includes/config.php の設定に従って自動実行
```

### 3. 設定ファイル
`includes/config.php` でデータベース接続情報を設定:

```php
define('DB_HOST', '192.168.33.10');
define('DB_NAME', 'sns_db');
define('DB_USER', 'sns_user');
define('DB_PASS', 'sns_pass');
```

### 4. ファイル権限
```bash
# Webサーバーユーザーに権限付与
chown -R www-data:www-data /var/www/html/
chmod -R 755 /var/www/html/
```

## 🧪 初期データ

### 管理者ユーザー
- **ユーザー名**: `admin`
- **パスワード**: `password`
- **表示名**: `Administrator`

## 🔧 カスタマイズ

### テーマ変更
`css/style.css` でカラーパレットを変更:

```css
:root {
    --primary-color: #1da1f2;
    --secondary-color: #14171a;
    --background-color: #f5f5f5;
}
```

### 機能拡張
- `includes/posts.php`: 投稿関連機能の拡張
- `includes/auth.php`: 認証機能の拡張
- `js/main.js`: フロントエンド機能の追加

## 📊 パフォーマンス

### 最適化ポイント
- データベースインデックスの活用
- 画像最適化（将来の拡張）
- CSS/JSの最小化（将来の拡張）

### 推奨設定
```php
// PHP設定
memory_limit = 256M
max_execution_time = 60
upload_max_filesize = 10M
```

## 🐛 トラブルシューティング

### よくある問題

1. **データベース接続エラー**
   - `includes/config.php`の設定確認
   - MySQL接続情報の確認

2. **セッションエラー**
   - PHPのセッション設定確認
   - ファイル権限の確認

3. **CSS/JSが読み込まれない**
   - パスの確認
   - ファイル権限の確認

### デバッグモード
開発時は以下の設定を有効にしてください:

```php
// includes/config.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📈 今後の拡張案

### Phase 2
- いいね機能
- コメント機能
- ユーザーフォロー機能

### Phase 3
- ファイルアップロード機能
- リアルタイム通知
- 検索機能

### Phase 4
- API提供
- モバイルアプリ対応
- 管理者機能

## 📄 ライセンス

このプロジェクトはLT発表用のデモアプリケーションです。
教育・学習目的での使用は自由ですが、本番環境での使用は推奨されません。

---

**作成者**: GitHub Copilot Assistant  
**作成日**: 2025年7月12日  
**用途**: LT発表用デモアプリケーション
