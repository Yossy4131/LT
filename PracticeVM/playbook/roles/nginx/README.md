# Nginx Role

Nginxウェブサーバーのインストールと設定を行うAnsibleロールです。

## 概要

このロールは、SNSアプリケーション用のNginxウェブサーバーを自動構築します。PHP-FPMとの連携設定、セキュリティヘッダーの追加、バーチャルホストの設定などを行います。

## 機能

- Nginxのインストール
- Nginxサービスの起動と自動起動設定
- デフォルトサイトの削除
- SNSアプリケーション用バーチャルホストの作成
- PHP-FPMとの連携設定
- セキュリティヘッダーの追加
- ドキュメントルートの権限設定
- PHP情報ファイルの作成

## 変数

### デフォルト値 (defaults/main.yml)

| 変数名 | デフォルト値 | 説明 |
|--------|-------------|------|
| `document_root` | "/var/www/html" | ウェブサイトのドキュメントルート |
| `php_version` | "8.1" | 連携するPHPのバージョン |

## 使用方法

### playbookでの使用例

```yaml
---
- name: Setup Web Server
  hosts: web_servers
  become: yes
  roles:
    - nginx
```

### 変数のカスタマイズ

```yaml
---
- name: Setup Web Server
  hosts: web_servers
  become: yes
  vars:
    document_root: "/var/www/myapp"
    php_version: "8.2"
  roles:
    - nginx
```

## Nginx設定

### バーチャルホスト設定

作成される設定ファイル: `/etc/nginx/sites-available/sns`

```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/html;
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    # Security headers
    add_header X-Frame-Options DENY;
    add_header X-Content-Type-Options nosniff;
    add_header X-XSS-Protection "1; mode=block";
}
```

### セキュリティ設定

- **X-Frame-Options**: クリックジャッキング攻撃を防止
- **X-Content-Type-Options**: MIME タイプスニッフィングを防止
- **X-XSS-Protection**: XSS攻撃の防止
- **.htaccessファイルへのアクセス拒否**

## ファイル構成

```
/etc/nginx/
├── sites-available/
│   └── sns                 # SNSアプリケーション設定
├── sites-enabled/
│   └── sns -> ../sites-available/sns
└── nginx.conf             # メイン設定ファイル
```

## 権限設定

- ドキュメントルート所有者: `www-data:www-data`
- ディレクトリパーミッション: `755`

## 依存関係

### システム要件

- Ubuntu 22.04 LTS
- PHP-FPM (推奨: php8.1-fpm)

### 前提ロール

このロールは以下のロールと組み合わせて使用することを想定しています:

- `php` ロール (PHP-FPMのセットアップ)

## トラブルシューティング

### Nginx設定のテスト

```bash
sudo nginx -t
```

### Nginxサービスの状態確認

```bash
sudo systemctl status nginx
```

### PHP-FPMとの連携確認

```bash
# PHP-FPMソケットの確認
ls -la /var/run/php/php8.1-fpm.sock

# PHP情報ページでの確認
curl http://localhost/info.php
```

### エラーログの確認

```bash
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log
```

### 権限問題の解決

```bash
# ファイル所有者の確認
ls -la /var/www/html/

# 権限の修正
sudo chown -R www-data:www-data /var/www/html/
sudo chmod -R 755 /var/www/html/
```

## パフォーマンス最適化

### 推奨設定 (将来の拡張用)

```nginx
# worker_processes の最適化
worker_processes auto;

# gzip圧縮の有効化
gzip on;
gzip_vary on;
gzip_types text/plain text/css application/json application/javascript;

# キープアライブの設定
keepalive_timeout 65;
```

## ログ

- アクセスログ: `/var/log/nginx/access.log`
- エラーログ: `/var/log/nginx/error.log`

## SSL/HTTPS設定 (将来の拡張)

このロールは現在HTTP(80番ポート)のみサポートしていますが、将来的にSSL証明書の設定も追加可能です。

```nginx
server {
    listen 443 ssl http2;
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    # その他のSSL設定...
}
```
