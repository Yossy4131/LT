<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/posts.php';

// ログインチェック
requireLogin();

$currentUser = getCurrentUser();

// ユーザーの投稿を取得
try {
    $userPosts = getUserPosts($currentUser['id']);
    $userPostCount = count($userPosts);
} catch (Exception $e) {
    $error = $e->getMessage();
    $userPosts = [];
    $userPostCount = 0;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール - <?php echo h(SITE_NAME); ?></title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <h1 class="site-title"><?php echo h(SITE_NAME); ?></h1>
            <nav class="nav">
                <a href="../../index.php" class="nav-link">ホーム</a>
                <a href="../auth/logout.php" class="nav-link">ログアウト</a>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <section class="profile-section">
                <h2>プロフィール</h2>
                <div class="profile-card">
                    <div class="profile-info">
                        <h3 class="profile-name"><?php echo h($currentUser['display_name']); ?></h3>
                        <p class="profile-username">@<?php echo h($currentUser['username']); ?></p>
                        <p class="profile-stats">投稿数: <?php echo h($userPostCount); ?>件</p>
                    </div>
                </div>
            </section>

            <section class="posts-section">
                <h2>あなたの投稿</h2>
                
                <?php if (empty($userPosts)): ?>
                    <p class="no-posts">まだ投稿がありません。<a href="../../index.php">新しい投稿をしてみましょう！</a></p>
                <?php else: ?>
                    <div class="posts-list">
                        <?php foreach ($userPosts as $post): ?>
                            <article class="post">
                                <div class="post-header">
                                    <h3 class="post-author"><?php echo h($post['display_name']); ?></h3>
                                    <span class="post-username">@<?php echo h($post['username']); ?></span>
                                    <time class="post-date"><?php echo h(formatDateTime($post['created_at'])); ?></time>
                                </div>
                                <div class="post-content">
                                    <?php echo nl2br(h($post['content'])); ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 <?php echo h(SITE_NAME); ?>. LT発表用デモアプリケーション</p>
        </div>
    </footer>

    <script src="../../js/main.js"></script>
</body>
</html>
