<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/posts.php';

// ログインが必要でない場合は認証ページにリダイレクト
if (!isLoggedIn()) {
    header('Location: pages/auth/login.php');
    exit();
}

$error = '';
$success = '';

// 投稿処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'post') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'セキュリティエラーが発生しました。';
    } else {
        $content = trim($_POST['content'] ?? '');
        $validationError = validatePostContent($content);
        
        if ($validationError) {
            $error = $validationError;
        } else {
            try {
                createPost($_SESSION['user_id'], $content);
                $success = '投稿が作成されました。';
                // リダイレクトしてPOSTデータをクリア
                header('Location: index.php');
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }
}

// 投稿一覧の取得
try {
    $posts = getPosts();
    $postCount = getPostCount();
} catch (Exception $e) {
    $error = $e->getMessage();
    $posts = [];
    $postCount = 0;
}

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h(SITE_NAME); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <h1 class="site-title"><?php echo h(SITE_NAME); ?></h1>
            <nav class="nav">
                <span class="user-info">
                    ようこそ、<?php echo h($currentUser['display_name']); ?>さん
                </span>
                <a href="pages/user/profile.php" class="nav-link">プロフィール</a>
                <a href="pages/auth/logout.php" class="nav-link">ログアウト</a>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo h($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo h($success); ?></div>
            <?php endif; ?>

            <!-- 投稿フォーム -->
            <section class="post-form-section">
                <h2>新しい投稿</h2>
                <form method="POST" class="post-form">
                    <input type="hidden" name="action" value="post">
                    <input type="hidden" name="csrf_token" value="<?php echo h(generateCsrfToken()); ?>">
                    
                    <div class="form-group">
                        <textarea name="content" placeholder="今何をしていますか？" 
                                class="post-textarea" maxlength="1000" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">投稿する</button>
                </form>
            </section>

            <!-- 投稿一覧 -->
            <section class="posts-section">
                <h2>最新の投稿 (<?php echo h($postCount); ?>件)</h2>
                
                <?php if (empty($posts)): ?>
                    <p class="no-posts">まだ投稿がありません。最初の投稿をしてみましょう！</p>
                <?php else: ?>
                    <div class="posts-list">
                        <?php foreach ($posts as $post): ?>
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

    <script src="js/main.js"></script>
</body>
</html>
