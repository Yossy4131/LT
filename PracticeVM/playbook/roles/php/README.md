# PHP Role

PHP-FPMとPHP拡張モジュールのインストールと設定を行うAnsibleロールです。

## 概要

このロールは、SNSアプリケーション用のPHP実行環境を自動構築します。PHP-FPM、必要な拡張モジュール、最適化された設定を一括で行います。

## 機能

- PHP-FPMのインストール
- 必要なPHP拡張モジュールのインストール
- PHP-FPMサービスの起動と自動起動設定
- PHP-FPMプール設定の最適化
- PHP設定ファイルの最適化
- SNSアプリケーション用の設定調整

## インストールされるPHP拡張モジュール

| 拡張モジュール | 用途 |
|---------------|------|
| php-mysql | MySQL PDO接続 |
| php-mysqli | MySQL拡張接続 |
| php-curl | HTTP通信 |
| php-json | JSON処理 |
| php-mbstring | マルチバイト文字列処理 |
| php-xml | XML処理 |
| php-zip | ZIP圧縮・展開 |
| php-gd | 画像処理 |
| php-intl | 国際化機能 |

## 変数

### デフォルト値 (defaults/main.yml)

| 変数名 | デフォルト値 | 説明 |
|--------|-------------|------|
| `php_version` | "8.1" | PHPのバージョン |
| `max_file_size` | "10M" | アップロード可能な最大ファイルサイズ |
| `max_post_size` | "20M" | POST送信の最大サイズ |
| `max_execution_time` | "60" | 最大実行時間（秒） |
| `memory_limit` | "256M" | メモリ制限 |
| `display_errors` | "Off" | エラー表示の有無 |
| `timezone` | "Asia/Tokyo" | タイムゾーン |

## 使用方法

### playbookでの使用例

```yaml
---
- name: Setup PHP Environment
  hosts: web_servers
  become: yes
  roles:
    - php
```

### 変数のカスタマイズ

```yaml
---
- name: Setup PHP Environment
  hosts: web_servers
  become: yes
  vars:
    php_version: "8.2"
    max_file_size: "50M"
    max_post_size: "100M"
    memory_limit: "512M"
    timezone: "UTC"
  roles:
    - php
```

## PHP-FPM設定

### プール設定

設定ファイル: `/etc/php/8.1/fpm/pool.d/www.conf`

主要な設定項目:
- **user/group**: www-data
- **listen**: Unix socket (`/var/run/php/php8.1-fpm.sock`)
- **listen.owner/group**: www-data
- **listen.mode**: 0660

### PHP設定

設定ファイル: `/etc/php/8.1/fpm/php.ini`

最適化される設定項目:
- ファイルアップロード制限
- POST送信制限
- 実行時間制限
- メモリ制限
- エラー表示設定
- タイムゾーン設定

## ファイル構成

```
/etc/php/8.1/
├── fpm/
│   ├── php.ini             # PHP設定ファイル
│   └── pool.d/
│       └── www.conf        # FPMプール設定
├── cli/
│   └── php.ini            # CLI用PHP設定
└── mods-available/        # 利用可能モジュール
```

## サービス管理

### サービス名
- `php8.1-fpm`

### 基本操作
```bash
# サービス状態確認
sudo systemctl status php8.1-fpm

# サービス再起動
sudo systemctl restart php8.1-fpm

# サービス停止
sudo systemctl stop php8.1-fpm

# サービス開始
sudo systemctl start php8.1-fpm
```

## 依存関係

### システム要件
- Ubuntu 22.04 LTS
- パッケージマネージャー (apt)

### 連携ロール
このロールは以下のロールと組み合わせて使用することを想定しています:
- `nginx` ロール (Nginxとの連携)

## トラブルシューティング

### PHP-FPMの状態確認

```bash
# サービス状態
sudo systemctl status php8.1-fpm

# プロセス確認
ps aux | grep php-fpm

# ソケットファイル確認
ls -la /var/run/php/php8.1-fpm.sock
```

### 設定ファイルの確認

```bash
# PHP設定確認
php -i | grep "Configuration File"

# FPM設定テスト
sudo php-fpm8.1 -t

# モジュール確認
php -m
```

### エラーログの確認

```bash
# PHP-FPMエラーログ
sudo tail -f /var/log/php8.1-fpm.log

# システムログ
sudo journalctl -u php8.1-fpm
```

### 権限問題の解決

```bash
# ソケットファイルの権限確認
ls -la /var/run/php/

# 権限修正
sudo chown www-data:www-data /var/run/php/php8.1-fpm.sock
sudo chmod 660 /var/run/php/php8.1-fpm.sock
```

## パフォーマンス最適化

### 推奨設定 (将来の拡張用)

```ini
; プロセス管理
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35

; OPcache設定
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
```

## セキュリティ設定

- **display_errors**: 本番環境では無効化
- **expose_php**: PHPバージョン情報の非表示
- **allow_url_fopen**: 外部URLアクセスの制限
- **file_uploads**: ファイルアップロードの適切な制限

## ログ

- PHP-FPMログ: `/var/log/php8.1-fpm.log`
- PHPエラーログ: 設定により変更可能

## 開発環境用設定

開発環境では以下の設定を有効にすることを推奨:

```yaml
vars:
  display_errors: "On"
  log_errors: "On"
  error_reporting: "E_ALL"
```

## 本番環境用設定

本番環境では以下の設定を推奨:

```yaml
vars:
  display_errors: "Off"
  log_errors: "On"
  error_reporting: "E_ALL & ~E_DEPRECATED & ~E_STRICT"
  expose_php: "Off"
```
