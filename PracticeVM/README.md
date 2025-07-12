# PracticeVM - LT発表用仮想環境構築

このディレクトリには、LT発表用簡易SNSアプリケーションの仮想環境構築に必要なファイルが含まれています。

## 📁 ディレクトリ構成

```
PracticeVM/
├── Vagrantfile              # Vagrant設定ファイル
├── playbook/               # Ansibleプレイブック
│   ├── db-server.yml       # DBサーバー用プレイブック
│   └── web-server.yml      # Webサーバー用プレイブック
├── roles/                  # Ansibleロール
│   ├── mysql/              # MySQLロール
│   ├── nginx/              # Nginxロール
│   ├── php/                # PHPロール
│   └── README.md           # ロールの詳細説明
└── README.md               # このファイル
```

## 🏗️ インフラ構成

### DBサーバーVM
- **ホスト名**: db-server
- **IPアドレス**: 192.168.33.10
- **OS**: Ubuntu 22.04 LTS
- **ソフトウェア**: MySQL 8.0
- **メモリ**: 1GB
- **CPU**: 1コア

### WebサーバーVM
- **ホスト名**: web-server
- **IPアドレス**: 192.168.33.11
- **OS**: Ubuntu 22.04 LTS
- **ソフトウェア**: Nginx + PHP 8.1
- **メモリ**: 1GB
- **CPU**: 1コア
- **ポートフォワーディング**: 8080 → 80

## 🚀 クイックスタート

### 1. 前提条件の確認
以下のソフトウェアがインストールされていることを確認してください：

- VirtualBox 7.0以上
- Vagrant 2.3以上

### 2. 仮想マシンの起動
```bash
# PracticeVMディレクトリに移動
cd C:\codes\LT\PracticeVM

# すべてのVMを起動（プロビジョニング含む）
vagrant up

# または個別に起動
vagrant up db-server
vagrant up web-server
```

### 3. 起動確認
```bash
# VM状態の確認
vagrant status

# サービス状態の確認（SSH接続）
vagrant ssh db-server -c "sudo systemctl status mysql"
vagrant ssh web-server -c "sudo systemctl status nginx"
vagrant ssh web-server -c "sudo systemctl status php8.1-fpm"
```

### 4. アプリケーションへのアクセス

**カスタムドメイン（推奨）**:
- **メインページ**: http://localhost.demosns
- **代替ドメイン**: http://demo.sns.local
- **PHP情報**: http://localhost.demosns/info.php

**従来方式**:
- **メインページ**: http://localhost:8080
- **PHP情報**: http://localhost:8080/info.php

**初期ログインユーザー**: `admin` / `password`

### 5. カスタムドメインについて
- vagrant-hostmanager プラグインにより自動的にホストファイルが更新されます
- Windows、macOS、Linuxすべてで動作します
- プラグインが未インストールの場合は自動インストールされます

## 🔧 Ansibleロール詳細

### mysql ロール
**場所**: `roles/mysql/`
**機能**: MySQLサーバーの設定とSNSデータベースの構築

**主なタスク**:
- MySQL Server 8.0のインストール
- ルートパスワードの設定 (`root123`)
- SNSデータベース (`sns_db`) の作成
- アプリケーション用ユーザー (`sns_user`) の作成
- テーブル構造の作成 (users, posts)
- 初期管理者ユーザーの挿入
- リモートアクセスの許可設定

### nginx ロール

**場所**: `roles/nginx/`  
**機能**: Nginxウェブサーバーの設定とパス自動修正

**主なタスク**:

- Nginxのインストールと起動
- デフォルトサイトの無効化
- SNSアプリケーション用バーチャルホストの設定
- PHP-FPMとの連携設定
- セキュリティヘッダーの追加
- **自動パス修正**: PHPファイルの相対パス自動修正機能
- **HTMLリンク修正**: ナビゲーションリンクの自動修正
- ドキュメントルートの権限設定

### php ロール
**場所**: `roles/php/`
**機能**: PHP-FPMとPHP拡張モジュールの設定

**主なタスク**:
- PHP 8.1とFPMのインストール
- 必要なPHP拡張モジュールのインストール
- PHP-FPMプールの設定
- PHPの設定最適化 (タイムゾーン、メモリ制限等)

## 📋 管理コマンド

### 基本操作
```bash
# VM起動
vagrant up

# VM停止
vagrant halt

# VM再起動
vagrant reload

# VM削除
vagrant destroy

# SSH接続
vagrant ssh [vm-name]

# プロビジョニングのみ実行
vagrant provision [vm-name]
```

### トラブルシューティング
```bash
# VM状態確認
vagrant global-status

# ログ確認
vagrant ssh web-server -c "sudo tail -f /var/log/nginx/error.log"
vagrant ssh db-server -c "sudo tail -f /var/log/mysql/error.log"

# サービス再起動
vagrant ssh web-server -c "sudo systemctl restart nginx"
vagrant ssh web-server -c "sudo systemctl restart php8.1-fpm"
vagrant ssh db-server -c "sudo systemctl restart mysql"
```

## 🔒 セキュリティ設定

### データベース
- ルートパスワード: `root123`
- アプリケーションユーザー: `sns_user` / `sns_pass`
- リモートアクセス制限: 192.168.33.11からのみ許可

### ウェブサーバー
- セキュリティヘッダーの設定
- .htaccessファイルへのアクセス禁止
- PHPエラー表示の無効化

## 📊 データベース構成

### テーブル構造

#### users テーブル
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### posts テーブル
```sql
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### 初期データ
- 管理者ユーザー: `admin` / `password`

## 🎯 LT発表での活用

### 1. インフラ自動化のデモ
```bash
# 一発でインフラ構築
vagrant up
```

### 2. 設定の説明ポイント
- Vagrantfileでのマルチマシン構成
- Ansibleによる自動プロビジョニング
- ロールベースの設定管理
- サーバー分離設計

### 3. 技術的特徴
- Infrastructure as Code
- 冪等性のあるプロビジョニング
- 再利用可能なロール設計
- セキュリティベストプラクティス

## 🔍 よくある問題と解決方法

### VM起動エラー
```bash
# VirtualBoxのバージョン確認
VBoxManage --version

# Vagrantプラグインの更新
vagrant plugin update

# VM強制削除後の再起動
vagrant destroy -f
vagrant up
```

### ネットワーク接続エラー
```bash
# ネットワーク設定確認
vagrant ssh web-server -c "ip addr show"
vagrant ssh db-server -c "ip addr show"

# ファイアウォール確認
vagrant ssh db-server -c "sudo ufw status"
```

### プロビジョニングエラー
```bash
# Ansibleバージョン確認
vagrant ssh web-server -c "ansible --version"

# 手動プロビジョニング実行
vagrant provision db-server
vagrant provision web-server
```

## 📚 参考資料

- [Vagrant Documentation](https://www.vagrantup.com/docs)
- [Ansible Documentation](https://docs.ansible.com/)
- [VirtualBox Manual](https://www.virtualbox.org/manual/)

## 🏷️ バージョン情報

- **作成日**: 2025年7月12日
- **Vagrant**: 2.3+
- **VirtualBox**: 7.0+
- **Ubuntu**: 22.04 LTS
- **MySQL**: 8.0
- **PHP**: 8.1
- **Nginx**: 1.18

---
**注意**: このプロジェクトはLT発表用のデモンストレーション目的で作成されており、本番環境での使用は想定していません。
