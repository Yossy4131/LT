<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

// ログアウト処理
logoutUser();

// ログインページにリダイレクト
header('Location: login.php');
exit();
?>
