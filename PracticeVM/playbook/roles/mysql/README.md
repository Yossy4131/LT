# MySQL Role

MySQLデータベースサーバーのインストールと設定を行うAnsibleロールです。

## 概要

このロールは、SNSアプリケーション用のMySQLデータベースサーバーを自動構築します。データベース、ユーザー、テーブルの作成からセキュリティ設定まで一括で行います。

## 機能

- MySQL Server 8.0のインストール
- MySQLサービスの起動と自動起動設定
- ルートユーザーのパスワード設定
- SNSアプリケーション用データベースの作成
- アプリケーション用ユーザーの作成と権限設定
- リモートアクセスの許可設定
- テーブル構造の作成 (users, posts)
- 初期管理者ユーザーの挿入

## 変数

### デフォルト値 (defaults/main.yml)

| 変数名 | デフォルト値 | 説明 |
|--------|-------------|------|
| `mysql_root_password` | "root123" | MySQLルートユーザーのパスワード |
| `sns_db_name` | "sns_db" | SNSアプリケーション用データベース名 |
| `sns_db_user` | "sns_user" | SNSアプリケーション用ユーザー名 |
| `sns_db_password` | "sns_pass" | SNSアプリケーション用ユーザーのパスワード |
| `allowed_host` | "192.168.33.11" | データベースアクセスを許可するホスト |

## 使用方法

### playbookでの使用例

```yaml
---
- name: Setup Database Server
  hosts: db_servers
  become: yes
  roles:
    - mysql
```

### 変数のカスタマイズ

```yaml
---
- name: Setup Database Server
  hosts: db_servers
  become: yes
  vars:
    mysql_root_password: "my_secure_password"
    sns_db_name: "my_app_database"
    sns_db_user: "app_user"
    sns_db_password: "app_password"
  roles:
    - mysql
```

## 作成されるデータベース構造

### users テーブル

| カラム名 | データ型 | 制約 | 説明 |
|----------|----------|------|------|
| id | INT | AUTO_INCREMENT, PRIMARY KEY | ユーザーID |
| username | VARCHAR(50) | UNIQUE, NOT NULL | ユーザー名 |
| password_hash | VARCHAR(255) | NOT NULL | パスワードハッシュ |
| display_name | VARCHAR(100) | NOT NULL | 表示名 |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | 作成日時 |

### posts テーブル

| カラム名 | データ型 | 制約 | 説明 |
|----------|----------|------|------|
| id | INT | AUTO_INCREMENT, PRIMARY KEY | 投稿ID |
| user_id | INT | NOT NULL, FOREIGN KEY | ユーザーID |
| content | TEXT | NOT NULL | 投稿内容 |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | 投稿日時 |

## 初期データ

### 管理者ユーザー

| フィールド | 値 |
|-----------|-----|
| username | admin |
| password | password |
| display_name | Administrator |

## セキュリティ設定

- ルートパスワードの設定
- 特定ホストからのアクセスのみ許可
- リモート接続の設定

## 依存関係

### システム要件

- Ubuntu 22.04 LTS
- Python3
- python3-pymysql パッケージ

### Ansible要件

- ansible.builtin.apt
- community.mysql コレクション

## トラブルシューティング

### MySQLサービスが起動しない場合

```bash
sudo systemctl status mysql
sudo journalctl -u mysql
```

### データベース接続ができない場合

```bash
# ローカル接続確認
mysql -u root -p

# リモート接続確認
mysql -h 192.168.33.10 -u sns_user -p sns_db
```

### 設定ファイルの確認

```bash
sudo cat /etc/mysql/mysql.conf.d/mysqld.cnf | grep bind-address
```

## ログ

- MySQLエラーログ: `/var/log/mysql/error.log`
- MySQL一般ログ: `/var/log/mysql/mysql.log` (有効化時)
