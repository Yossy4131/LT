<?php
require_once __DIR__ . '/config.php';

// ログイン状態の確認
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

// ログインが必要なページでのチェック
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// ユーザー登録
function registerUser($username, $password, $displayName) {
    try {
        $pdo = getDbConnection();
        
        // ユーザー名の重複チェック
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            throw new Exception("このユーザー名は既に使用されています。");
        }
        
        // パスワードのハッシュ化
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // ユーザーの登録
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, display_name) VALUES (?, ?, ?)");
        $stmt->execute([$username, $passwordHash, $displayName]);
        
        return true;
    } catch (Exception $e) {
        error_log("User registration failed: " . $e->getMessage());
        throw $e;
    }
}

// ユーザーログイン
function loginUser($username, $password) {
    try {
        $pdo = getDbConnection();
        
        $stmt = $pdo->prepare("SELECT id, username, password_hash, display_name FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // セッションの開始とユーザー情報の保存
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['display_name'] = $user['display_name'];
            $_SESSION['login_time'] = time();
            
            return true;
        }
        
        return false;
    } catch (Exception $e) {
        error_log("User login failed: " . $e->getMessage());
        throw new Exception("ログインに失敗しました。");
    }
}

// ログアウト
function logoutUser() {
    session_destroy();
    session_start();
}

// 現在のユーザー情報を取得
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'display_name' => $_SESSION['display_name']
    ];
}

// ユーザー名のバリデーション
function validateUsername($username) {
    if (empty($username)) {
        return "ユーザー名は必須です。";
    }
    if (strlen($username) < 3) {
        return "ユーザー名は3文字以上で入力してください。";
    }
    if (strlen($username) > 50) {
        return "ユーザー名は50文字以下で入力してください。";
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return "ユーザー名は英数字とアンダースコアのみ使用できます。";
    }
    return null;
}

// パスワードのバリデーション
function validatePassword($password) {
    if (empty($password)) {
        return "パスワードは必須です。";
    }
    if (strlen($password) < 6) {
        return "パスワードは6文字以上で入力してください。";
    }
    return null;
}
?>
