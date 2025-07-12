# LT発表用簡易SNS プロジェクト概要

## プロジェクト名
Simple SNS for LT Presentation

## 目的
LT発表において、AI活用事例として、VirtualBoxとVagrantを用いて構築したVM上で動作する簡易SNSのデモンストレーションを行う。

## アーキテクチャ
- **DBサーバーVM**: MySQL 8.0 (192.168.33.10)
- **WebサーバーVM**: Nginx + PHP 8.1 (192.168.33.11)
- **プロビジョニング**: Ansible
- **仮想化**: VirtualBox + Vagrant

## 技術スタック

### インフラ
- VirtualBox 7.0+
- Vagrant 2.3+
- Ansible (local provisioning)
- Ubuntu 22.04 LTS

### バックエンド
- PHP 8.1
- MySQL 8.0
- Nginx 1.18

### フロントエンド
- HTML5
- CSS3 (Flexbox, Grid)
- JavaScript (ES6+)

### セキュリティ
- PDO Prepared Statements (SQLインジェクション対策)
- HTMLエスケープ (XSS対策)
- CSRFトークン (CSRF対策)
- パスワードハッシュ化 (password_hash関数)

## ファイル構成

### PracticeVM/ (インフラコード)
```
PracticeVM/
├── Vagrantfile                 # VM定義
├── playbook/
│   ├── db-server.yml          # DBサーバープロビジョニング
│   └── web-server.yml         # Webサーバープロビジョニング
└── roles/                     # Ansible roles (将来の拡張用)
    └── README.md
```

### PracticeWeb/ (アプリケーションコード)
```
PracticeWeb/
├── index.php                  # メインページ
├── login.php                  # ログインページ
├── register.php               # ユーザー登録ページ
├── profile.php                # プロフィールページ
├── logout.php                 # ログアウト処理
├── includes/
│   ├── config.php            # 設定・DB接続
│   ├── auth.php              # 認証関連関数
│   └── posts.php             # 投稿関連関数
├── css/
│   └── style.css             # スタイルシート
├── js/
│   └── main.js               # JavaScript機能
└── .htaccess                 # Apache設定 (参考用)
```

## データベース設計

### users テーブル
| Column | Type | Description |
|--------|------|-------------|
| id | INT AUTO_INCREMENT PRIMARY KEY | ユーザーID |
| username | VARCHAR(50) UNIQUE NOT NULL | ユーザー名 |
| password_hash | VARCHAR(255) NOT NULL | パスワードハッシュ |
| display_name | VARCHAR(100) NOT NULL | 表示名 |
| created_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | 作成日時 |

### posts テーブル
| Column | Type | Description |
|--------|------|-------------|
| id | INT AUTO_INCREMENT PRIMARY KEY | 投稿ID |
| user_id | INT NOT NULL | ユーザーID (外部キー) |
| content | TEXT NOT NULL | 投稿内容 |
| created_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | 投稿日時 |

## 機能一覧

### 認証機能
- [x] ユーザー登録
- [x] ログイン/ログアウト
- [x] セッション管理
- [x] パスワード暗号化

### 投稿機能
- [x] 新規投稿作成
- [x] 投稿一覧表示
- [x] 投稿の時系列表示
- [x] 文字数制限 (1000文字)

### ユーザー機能
- [x] プロフィール表示
- [x] ユーザー別投稿表示

### UI/UX
- [x] レスポンシブデザイン
- [x] モダンなインターフェース
- [x] リアルタイム文字数カウンター
- [x] フォームバリデーション

## セキュリティ対策

### 実装済み
- SQLインジェクション対策 (PDO prepared statements)
- XSS対策 (htmlspecialchars関数)
- CSRF対策 (CSRFトークン検証)
- パスワード暗号化 (password_hash/password_verify)
- セッションハイジャック対策
- 入力値検証

### 注意事項
このアプリケーションはLT発表用のデモンストレーション目的で作成されており、本番環境での使用は想定していません。

## 構築・実行手順

### 1. 前提条件
- VirtualBox 7.0以上
- Vagrant 2.3以上

### 2. 環境構築
```bash
cd PracticeVM
vagrant up
```

### 3. アクセス
- URL: http://localhost:8080
- 初期ユーザー: admin / password

## LT発表での活用ポイント

### 1. インフラ自動化のアピール
- Vagrantによる環境の自動構築
- Ansibleによるプロビジョニング
- Infrastructure as Code の実践

### 2. 技術的な特徴
- マイクロサービス的な設計 (DB/Web分離)
- モダンなWeb技術の活用
- セキュリティベストプラクティスの実装

### 3. デモシナリオ
1. 環境構築の自動化 (`vagrant up`)
2. アプリケーション機能のデモ
3. コード品質とセキュリティ対策の説明

## 今後の拡張可能性

### 機能拡張
- [ ] いいね機能
- [ ] コメント機能
- [ ] ユーザーフォロー機能
- [ ] 画像投稿機能
- [ ] WebSocket によるリアルタイム更新

### インフラ拡張
- [ ] Docker コンテナ化
- [ ] Kubernetes デプロイメント
- [ ] CI/CD パイプライン
- [ ] ロードバランサー設定
- [ ] SSL/TLS 証明書設定

## ライセンス
MIT License (デモ用途)

## 作成者
GitHub Copilot Assistant

## 作成日
2025年7月12日
