<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

// 既にログインしている場合はリダイレクト
if (isLoggedIn()) {
    header('Location: ../../index.php');
    exit();
}

$error = '';

// 登録処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'セキュリティエラーが発生しました。';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $displayName = trim($_POST['display_name'] ?? '');
        
        // バリデーション
        $usernameError = validateUsername($username);
        $passwordError = validatePassword($password);
        
        if ($usernameError) {
            $error = $usernameError;
        } elseif ($passwordError) {
            $error = $passwordError;
        } elseif ($password !== $confirmPassword) {
            $error = 'パスワードが一致しません。';
        } elseif (empty($displayName)) {
            $error = '表示名は必須です。';
        } elseif (strlen($displayName) > 100) {
            $error = '表示名は100文字以下で入力してください。';
        } else {
            try {
                registerUser($username, $password, $displayName);
                
                // アカウント作成成功時はログイン画面にリダイレクト
                header('Location: login.php?message=' . urlencode('アカウントが作成されました。ログインしてください。'));
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録 - <?php echo h(SITE_NAME); ?></title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1 class="site-title"><?php echo h(SITE_NAME); ?></h1>
            <h2>新規登録</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo h($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo h(generateCsrfToken()); ?>">
                
                <div class="form-group">
                    <label for="username">ユーザー名</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo h($_POST['username'] ?? ''); ?>" 
                           required class="form-input"
                           pattern="[a-zA-Z0-9_]+"
                           title="英数字とアンダースコアのみ使用できます">
                    <small class="form-help">3-50文字、英数字とアンダースコアのみ</small>
                </div>
                
                <div class="form-group">
                    <label for="display_name">表示名</label>
                    <input type="text" id="display_name" name="display_name" 
                           value="<?php echo h($_POST['display_name'] ?? ''); ?>" 
                           required class="form-input" maxlength="100">
                    <small class="form-help">100文字以下</small>
                </div>
                
                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" 
                           required class="form-input" minlength="6">
                    <small class="form-help">6文字以上</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">パスワード（確認）</label>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           required class="form-input" minlength="6">
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">アカウント作成</button>
            </form>
            
            <div class="auth-links">
                <p>既にアカウントをお持ちの方は <a href="login.php">ログイン</a></p>
            </div>
        </div>
    </div>
    
    <script src="../../js/main.js"></script>
</body>
</html>
