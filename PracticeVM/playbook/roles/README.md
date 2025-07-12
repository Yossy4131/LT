# Ansible Roles Directory

このディレクトリには、再利用可能なAnsibleロールを配置します。

## 利用可能なロール

### mysql
MySQLデータベースサーバーの設定を行うロール

**主な機能:**
- MySQL Server 8.0のインストール
- ルートユーザーのパスワード設定
- SNSアプリケーション用データベースの作成
- アプリケーション用ユーザーの作成
- usersテーブルとpostsテーブルの作成
- 初期管理者ユーザーの挿入
- リモート接続の許可設定

**設定可能な変数:**
- `mysql_root_password`: MySQLルートパスワード (default: "root123")
- `sns_db_name`: SNSデータベース名 (default: "sns_db")
- `sns_db_user`: SNSユーザー名 (default: "sns_user")
- `sns_db_password`: SNSユーザーパスワード (default: "sns_pass")
- `allowed_host`: アクセス許可ホスト (default: "192.168.33.11")

### nginx
Nginxウェブサーバーの設定を行うロール

**主な機能:**
- Nginxのインストール
- サービスの開始と自動起動設定
- デフォルトサイトの削除
- SNSアプリケーション用のバーチャルホスト設定
- セキュリティヘッダーの追加
- ドキュメントルートの権限設定
- PHP情報ファイルの作成

**設定可能な変数:**
- `document_root`: ドキュメントルート (default: "/var/www/html")
- `php_version`: PHPバージョン (default: "8.1")

### php
PHP-FPMとPHP拡張モジュールの設定を行うロール

**主な機能:**
- PHP-FPMとPHP拡張モジュールのインストール
- PHP-FPMプールの設定
- PHP設定ファイルの最適化
- SNSアプリケーション用のPHP設定

**インストールされる拡張モジュール:**
- php-mysql, php-mysqli (データベース接続)
- php-curl (HTTP通信)
- php-json (JSON処理)
- php-mbstring (マルチバイト文字列)
- php-xml (XML処理)
- php-zip (ZIP処理)
- php-gd (画像処理)
- php-intl (国際化機能)

**設定可能な変数:**
- `php_version`: PHPバージョン (default: "8.1")
- `max_file_size`: 最大ファイルサイズ (default: "10M")
- `max_post_size`: 最大POSTサイズ (default: "20M")
- `max_execution_time`: 最大実行時間 (default: "60")
- `memory_limit`: メモリ制限 (default: "256M")
- `display_errors`: エラー表示 (default: "Off")
- `timezone`: タイムゾーン (default: "Asia/Tokyo")

## ロールの使用方法

playbookでロールを使用する例:

```yaml
---
- name: Setup Web Server
  hosts: localhost
  become: yes
  roles:
    - php
    - nginx

- name: Setup DB Server
  hosts: localhost
  become: yes
  roles:
    - mysql
```

## ディレクトリ構造

```
roles/
├── mysql/
│   ├── tasks/main.yml        # メインタスク
│   ├── defaults/main.yml     # デフォルト変数
│   └── handlers/main.yml     # ハンドラー
├── nginx/
│   ├── tasks/main.yml
│   ├── defaults/main.yml
│   └── handlers/main.yml
└── php/
    ├── tasks/main.yml
    ├── defaults/main.yml
    └── handlers/main.yml
```

## カスタマイズ

各ロールの変数は以下の方法でカスタマイズできます:

1. **playbookで直接指定:**
```yaml
- name: Setup MySQL
  hosts: localhost
  become: yes
  vars:
    mysql_root_password: "my_secure_password"
    sns_db_name: "my_app_db"
  roles:
    - mysql
```

2. **group_vars/host_varsファイルで指定:**
```
group_vars/
└── all.yml
```

3. **defaults/main.ymlファイルを直接編集**
