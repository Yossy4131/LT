# セキュリティ設計書

## 🔒 概要

簡易SNSアプリケーションに実装されているセキュリティ対策の詳細設計書です。
OWASP Top 10 の脅威に対する対策を中心に、包括的なセキュリティ機能を実装しています。

## 🛡️ セキュリティ対策一覧

### 1. SQLインジェクション対策

**脅威レベル**: 高
**対策**: PDO Prepared Statements

#### 実装詳細
```php
// ❌ 危険な実装例
$query = "SELECT * FROM users WHERE username = '{$username}'";

// ✅ 安全な実装（実際の実装）
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
```

#### 適用箇所
- **ユーザー認証**: `includes/auth.php`
- **投稿機能**: `includes/posts.php`
- **ユーザー登録**: `pages/auth/register.php`
- **プロフィール**: `pages/user/profile.php`

### 2. XSS（Cross-Site Scripting）対策

**脅威レベル**: 高
**対策**: HTMLエスケープ処理

#### 実装詳細
```php
// 出力時の自動エスケープ
function escape_html($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// 使用例
echo escape_html($user_input);
```

#### 適用箇所
- **投稿内容表示**: `index.php`
- **ユーザー名表示**: 全ページ
- **エラーメッセージ**: 各フォーム
- **プロフィール情報**: `pages/user/profile.php`

### 3. CSRF（Cross-Site Request Forgery）対策

**脅威レベル**: 中
**対策**: CSRFトークン検証

#### 実装詳細
```php
// トークン生成
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// トークン検証
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token);
}
```

#### 適用箇所
- **ログインフォーム**: `pages/auth/login.php`
- **登録フォーム**: `pages/auth/register.php`
- **投稿フォーム**: `index.php`
- **ログアウト**: `pages/auth/logout.php`

### 4. パスワード保護

**脅威レベル**: 高
**対策**: bcrypt暗号化

#### 実装詳細
```php
// パスワードハッシュ化
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// パスワード検証
$is_valid = password_verify($input_password, $stored_hash);
```

#### セキュリティ仕様
- **アルゴリズム**: bcrypt（現在のPHPデフォルト）
- **コスト**: デフォルト（動的調整）
- **ソルト**: 自動生成（bcrypt内蔵機能）

### 5. セッション管理

**脅威レベル**: 中
**対策**: セキュアなセッション設定

#### セッション設定
```php
// セッション設定（config.php）
ini_set('session.cookie_httponly', 1);  // XSS対策
ini_set('session.cookie_secure', 0);    // HTTPS環境では1に設定
ini_set('session.use_strict_mode', 1);  // セッション固定化攻撃対策
ini_set('session.cookie_samesite', 'Strict'); // CSRF対策
```

#### セッション機能
- **自動有効期限**: 24時間
- **セッション再生成**: ログイン時
- **セキュアクッキー**: HTTPS環境対応

### 6. 入力値検証（バリデーション）

**脅威レベル**: 中
**対策**: 包括的な入力チェック

#### ユーザー名バリデーション
```php
function validate_username($username) {
    // 長さチェック（3-20文字）
    if (strlen($username) < 3 || strlen($username) > 20) {
        return false;
    }
    
    // 文字種チェック（英数字とアンダースコアのみ）
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return false;
    }
    
    return true;
}
```

#### パスワードバリデーション
```php
function validate_password($password) {
    // 最小長チェック（8文字以上）
    if (strlen($password) < 8) {
        return false;
    }
    
    // 将来的な強度チェック拡張予定
    // - 大文字小文字混在
    // - 数字含有
    // - 特殊文字含有
    
    return true;
}
```

#### 投稿内容バリデーション
```php
function validate_post_content($content) {
    // 空文字チェック
    if (empty(trim($content))) {
        return false;
    }
    
    // 最大長チェック（1000文字）
    if (strlen($content) > 1000) {
        return false;
    }
    
    return true;
}
```

## 🔐 認証・認可システム

### 認証フロー

1. **ログイン処理**
   ```
   ユーザー入力 → バリデーション → DB照合 → 
   パスワード検証 → セッション開始 → リダイレクト
   ```

2. **認証状態チェック**
   ```php
   function require_login() {
       if (!is_logged_in()) {
           header('Location: pages/auth/login.php');
           exit();
       }
   }
   ```

### 認可制御

- **ページアクセス制御**: ログイン必須ページの保護
- **データアクセス制御**: 自分の投稿のみ操作可能
- **機能制限**: 未ログインユーザーの機能制限

## 🌐 ネットワークセキュリティ

### HTTPセキュリティヘッダー

```nginx
# Nginx設定例（実装済み）
add_header X-Content-Type-Options nosniff;
add_header X-Frame-Options DENY;
add_header X-XSS-Protection "1; mode=block";
add_header Referrer-Policy strict-origin-when-cross-origin;
```

### HTTPS対応（将来予定）

- **SSL/TLS**: Let's Encrypt証明書
- **HSTS**: HTTP Strict Transport Security
- **セキュアクッキー**: secure属性有効化

## 🗄️ データベースセキュリティ

### 接続セキュリティ

```php
// データベース接続（config.php）
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false, // 真のプリペアドステートメント
];
```

### データ保護

- **暗号化**: パスワードのbcryptハッシュ化
- **最小権限の原則**: アプリケーション専用DBユーザー
- **ネットワーク分離**: プライベートネットワーク内通信

## 🚨 セキュリティ監査項目

### 自動チェック項目

1. **SQLインジェクション**: 全SQLクエリのプリペアドステートメント使用
2. **XSS**: 全出力のHTMLエスケープ確認
3. **CSRF**: 全フォームのトークン検証
4. **認証**: ログイン必須ページの保護確認

### 手動監査項目

1. **セッション管理**: セッション固定化攻撃テスト
2. **パスワード**: 辞書攻撃耐性テスト
3. **入力値検証**: 境界値・異常値テスト
4. **権限制御**: 権限昇格攻撃テスト

## 📋 セキュリティチェックリスト

### 開発時チェック

- [ ] 全DBクエリでプリペアドステートメント使用
- [ ] 全ユーザー入力のエスケープ処理
- [ ] CSRFトークンの実装
- [ ] パスワードの適切なハッシュ化
- [ ] セッション設定の適切な設定

### デプロイ前チェック

- [ ] HTTPS設定（本番環境）
- [ ] セキュリティヘッダーの設定
- [ ] 不要なファイルの削除
- [ ] デバッグ情報の無効化
- [ ] エラーログの設定

## 🚧 既知の制限事項

### 現在未実装の機能

1. **レート制限**: ブルートフォース攻撃対策
2. **IPアドレス制限**: 地理的・IP的アクセス制限
3. **2要素認証**: SMS・アプリ認証
4. **ログ監視**: セキュリティイベント監視

### LT発表用の簡略化

- **パスワード強度**: 基本的なチェックのみ
- **セッションタイムアウト**: 固定設定
- **ログ機能**: 基本的なエラーログのみ

## 🔮 将来的なセキュリティ強化案

### Phase 2: 基本強化

- **レート制限**: Redis/Memcachedを利用
- **ログ監視**: セキュリティログの詳細化
- **パスワードポリシー**: より厳格な規則

### Phase 3: 高度な対策

- **WAF導入**: Web Application Firewall
- **セキュリティスキャン**: 自動脆弱性検査
- **侵入検知**: IDS/IPS導入

### Phase 4: エンタープライズ対応

- **SAML/OAuth2**: 外部認証連携
- **監査ログ**: 包括的な操作ログ
- **暗号化**: データベース暗号化

## 📚 参考資料

### セキュリティ標準

- **OWASP Top 10**: [https://owasp.org/www-project-top-ten/](https://owasp.org/www-project-top-ten/)
- **PHP Security**: [https://www.php.net/manual/en/security.php](https://www.php.net/manual/en/security.php)
- **NIST Guidelines**: [https://www.nist.gov/cyberframework](https://www.nist.gov/cyberframework)

### 実装ガイド

- **CSRF対策**: [https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html)
- **パスワードハッシュ**: [https://www.php.net/manual/en/function.password-hash.php](https://www.php.net/manual/en/function.password-hash.php)

---

**文書バージョン**: 1.0  
**最終更新**: 2025年7月12日  
**作成者**: GitHub Copilot Assistant  
**レビュー**: セキュリティ専門家による監査推奨
