<?php
require_once __DIR__ . '/config.php';

// 投稿の作成
function createPost($userId, $content) {
    try {
        $pdo = getDbConnection();
        
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
        $stmt->execute([$userId, $content]);
        
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Post creation failed: " . $e->getMessage());
        throw new Exception("投稿の作成に失敗しました。");
    }
}

// 投稿一覧の取得
function getPosts($limit = 20, $offset = 0) {
    try {
        $pdo = getDbConnection();
        
        $stmt = $pdo->prepare("
            SELECT p.id, p.content, p.created_at, u.display_name, u.username
            FROM posts p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Get posts failed: " . $e->getMessage());
        throw new Exception("投稿の取得に失敗しました。");
    }
}

// 特定ユーザーの投稿を取得
function getUserPosts($userId, $limit = 20, $offset = 0) {
    try {
        $pdo = getDbConnection();
        
        $stmt = $pdo->prepare("
            SELECT p.id, p.content, p.created_at, u.display_name, u.username
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.user_id = ?
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$userId, $limit, $offset]);
        
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Get user posts failed: " . $e->getMessage());
        throw new Exception("ユーザーの投稿取得に失敗しました。");
    }
}

// 投稿数の取得
function getPostCount() {
    try {
        $pdo = getDbConnection();
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM posts");
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        error_log("Get post count failed: " . $e->getMessage());
        return 0;
    }
}

// 投稿内容のバリデーション
function validatePostContent($content) {
    if (empty(trim($content))) {
        return "投稿内容は必須です。";
    }
    if (strlen($content) > 1000) {
        return "投稿内容は1000文字以下で入力してください。";
    }
    return null;
}

// 日時のフォーマット
function formatDateTime($datetime) {
    $timestamp = strtotime($datetime);
    $now = time();
    $diff = $now - $timestamp;
    
    if ($diff < 60) {
        return "たった今";
    } elseif ($diff < 3600) {
        return floor($diff / 60) . "分前";
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . "時間前";
    } elseif ($diff < 2592000) {
        return floor($diff / 86400) . "日前";
    } else {
        return date('Y年m月d日', $timestamp);
    }
}
?>
