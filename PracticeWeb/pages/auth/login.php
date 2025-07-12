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
$message = $_GET['message'] ?? '';

// ログイン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'セキュリティエラーが発生しました。';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $error = 'ユーザー名とパスワードを入力してください。';
        } else {
            try {
                if (loginUser($username, $password)) {
                    header('Location: ../../index.php');
                    exit();
                } else {
                    $error = 'ユーザー名またはパスワードが正しくありません。';
                }
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
    <title>ログイン - <?php echo h(SITE_NAME); ?></title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1 class="site-title"><?php echo h(SITE_NAME); ?></h1>
            <h2>ログイン</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo h($error); ?></div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo h($message); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo h(generateCsrfToken()); ?>">
                
                <div class="form-group">
                    <label for="username">ユーザー名</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo h($_POST['username'] ?? ''); ?>" 
                           required class="form-input">
                </div>
                
                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" 
                           required class="form-input">
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">ログイン</button>
            </form>
            
            <div class="auth-links">
                <p>アカウントをお持ちでない方は <a href="register.php">新規登録</a></p>
            </div>
        </div>
    </div>
    
    <script src="../../js/main.js"></script>
</body>
</html>
