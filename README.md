# LT発表用簡易SNS プロジェクト

## プロジェクト概要

**プロジェクト名**: Simple SNS for LT Presentation  
**目的**: LT発表において、AI活用事例として、VirtualBoxとVagrantを用いて構築したVM上で動作する簡易SNSのデモンストレーションを行う

### アーキテクチャ
- **DBサーバーVM**: MySQL 8.0 (192.168.33.10)
- **WebサーバーVM**: Nginx + PHP 8.1 (192.168.33.11)
- **プロビジョニング**: Ansible
- **仮想化**: VirtualBox + Vagrant

## 🎯 主な機能

### SNSアプリケーション機能
- **ユーザー認証**: 登録・ログイン・ログアウト
- **投稿機能**: テキスト投稿（最大1000文字）
- **投稿一覧**: 全ユーザーの投稿を時系列表示
- **プロフィール**: ユーザー情報と投稿履歴

### インフラ自動化機能
- **VM自動構築**: Vagrantによる2台のVM自動起動
- **自動プロビジョニング**: Ansibleによるソフトウェア自動インストール・設定
- **サーバー分離設計**: DBサーバーとWebサーバーの分離構成

## 🔧 技術スタック

### インフラ
- VirtualBox 7.1+
- Vagrant 2.4+
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
- bcryptパスワードハッシュ化

## 📁 プロジェクト構成

```
LT/
├── README.md                    # このファイル
├── PracticeVM/                  # VM構築関連
│   ├── README.md               # VM構築手順書
│   ├── Vagrantfile             # VM設定ファイル
│   ├── playbook/               # Ansibleプレイブック
│   │   ├── db-server.yml      # DBサーバー設定
│   │   └── web-server.yml     # Webサーバー設定
│   └── roles/                  # Ansibleロール
│       ├── mysql/             # MySQL設定ロール
│       ├── nginx/             # Nginx設定ロール
│       └── php/               # PHP設定ロール
└── PracticeWeb/                # Webアプリケーション
    ├── README.md               # アプリケーション説明
    ├── index.php              # メインページ
    ├── pages/                 # ページファイル
    │   ├── auth/              # 認証関連
    │   └── user/              # ユーザー関連
    ├── includes/              # 共通PHPファイル
    ├── css/                   # スタイルシート
    ├── js/                    # JavaScript
    └── docs/                  # ドキュメント
        └── database_design.md # DB設計書
```

## 🚀 クイックスタート

### 前提条件
以下のソフトウェアがインストールされていること：
- VirtualBox 7.1以上
- Vagrant 2.4以上

### 構築手順

1. **プロジェクトディレクトリに移動**
```bash
cd C:\codes\LT\PracticeVM
```

2. **仮想マシンを起動（自動プロビジョニング）**
```bash
vagrant up
```

3. **アプリケーションにアクセス**

   - **推奨アクセス方法**:
     - <http://localhost.demosns> （カスタムドメイン・推奨）
     - <http://demo.sns.local> （カスタムドメイン・代替）
   - **従来方式**: <http://localhost:8080>
   - **初期ログインユーザー**: `admin` / `password`

### VM構成とアクセス情報

- **DBサーバー**: 192.168.33.10 (db-server)
- **Webサーバー**: 192.168.33.11 (web-server)  
- **カスタムドメイン**: vagrant-hostmanager プラグインによる自動設定
- **自動パス修正**: Ansibleによるファイルパス自動修正機能

### 詳細な構築手順
詳細な手順については、各ディレクトリのREADMEを参照してください：
- **VM構築**: `PracticeVM/README.md`
- **アプリケーション**: `PracticeWeb/README.md`
- **データベース設計**: `PracticeWeb/docs/database_design.md`

## � プロジェクト構成

```
LT/
├── README.md                         # このファイル（プロジェクト全体の説明）
├── PracticeVM/                       # VM構築・プロビジョニング
│   ├── README.md                     # VM構築の詳細手順
│   ├── Vagrantfile                   # Vagrant設定
│   ├── playbook/                     # Ansibleプレイブック
│   │   ├── db-server.yml            # DBサーバープロビジョニング
│   │   └── web-server.yml           # Webサーバープロビジョニング
│   └── roles/                       # 再利用可能なAnsibleロール
│       ├── README.md                # ロール詳細説明
│       ├── mysql/                   # MySQLロール
│       ├── nginx/                   # Nginxロール
│       └── php/                     # PHPロール
└── PracticeWeb/                     # Webアプリケーション
    ├── README.md                    # アプリケーション詳細
    ├── index.php                    # メインページ
    ├── pages/auth/                  # 認証ページ
    ├── pages/user/                  # ユーザーページ
    ├── includes/                    # 共通PHPライブラリ
    ├── css/                         # スタイルシート
    ├── js/                          # JavaScript
    └── docs/                        # アプリケーション設計書
        ├── database_design.md      # データベース設計
        ├── application_spec.md     # アプリケーション仕様
        ├── security.md             # セキュリティ設計  
        └── functions.md            # 関数・API仕様
```

## 🎯 プロジェクトの特徴

### 💡 AI活用開発

- GitHub Copilotによる全面的な開発支援
- インフラからアプリケーションまで一貫した設計
- ベストプラクティスの自動適用

### 🏗️ インフラストラクチャー

- **仮想化**: VirtualBox + Vagrant による開発環境の標準化
- **プロビジョニング**: Ansible role-based 自動構築
- **ネットワーク**: プライベートネットワーク構成（192.168.33.0/24）
- **構成**: DB/Web サーバー分離によるスケーラブル設計
- **自動化**: パス修正とデプロイメントの完全自動化

### 🔧 技術スタック

- **バックエンド**: PHP 8.1 + MySQL 8.0 + Nginx
- **フロントエンド**: モダンCSS（Flexbox/Grid）+ Vanilla JavaScript
- **セキュリティ**: 包括的な脆弱性対策実装
- **カスタムドメイン**: vagrant-hostmanager による自動DNS設定

### 📱 レスポンシブ対応

- モバイルファーストデザイン
- フレキシブルレイアウト
- タッチフレンドリーUI

### 🚀 新機能・改善点

- **アカウント作成フロー改善**: 新規登録成功時に自動でログイン画面に遷移
- **自動パス修正**: Ansibleによるファイルパス自動修正機能
- **カスタムドメイン**: localhost.demosns / demo.sns.local での簡単アクセス  
- **プラグイン最適化**: vagrant-hostmanager, vagrant-vbguest 等の自動インストール
- **エラー自動修正**: プロビジョニング時の相対パス問題を自動解決
- **プロジェクトクリーンアップ**: 冗長ファイルを削除し、構造を最適化

## 📊 データベース構成

### データベース情報
- **データベース名**: `sns_db`
- **DBMS**: MySQL 8.0
- **文字セット**: utf8mb4（絵文字対応）

### テーブル構造

#### users テーブル
- **id**: ユーザーID（主キー、自動増分）
- **username**: ログイン用ユーザー名（ユニーク）
- **password_hash**: bcrypt暗号化パスワード
- **display_name**: 表示名
- **created_at**: アカウント作成日時

#### posts テーブル
- **id**: 投稿ID（主キー、自動増分）
- **user_id**: 投稿者ID（外部キー → users.id）
- **content**: 投稿内容（最大1000文字）
- **created_at**: 投稿日時

詳細設計: `PracticeWeb/docs/database_design.md`

## 🎯 LT発表での活用ポイント

### 1. インフラ自動化のデモ
- Vagrantによる一発環境構築
- Ansibleによる自動プロビジョニング
- Infrastructure as Code の実践

### 2. 技術的特徴の説明
- マイクロサービス的な設計（DB/Web分離）
- モダンなWeb技術スタック
- セキュリティベストプラクティス

### 3. デモシナリオ
1. `vagrant up` コマンドでの自動環境構築
2. アプリケーション機能のライブデモ
3. コード品質とセキュリティ対策の解説

## 🔧 管理コマンド

### 基本操作

```bash
# VM起動（初回はプラグイン自動インストール & プロビジョニング実行）
vagrant up

# VM停止
vagrant halt

# VM削除・再作成
vagrant destroy && vagrant up

# 特定のVMのみ操作
vagrant up db-server
vagrant up web-server
```

### SSH接続

```bash
# Webサーバーに接続
vagrant ssh web-server

# DBサーバーに接続
vagrant ssh db-server
```

### サービス状態確認

```bash
# Webサーバーのサービス確認
vagrant ssh web-server -c "sudo systemctl status nginx"
vagrant ssh web-server -c "sudo systemctl status php8.1-fpm"

# DBサーバーのサービス確認
vagrant ssh db-server -c "sudo systemctl status mysql"
```

### プロビジョニング関連

```bash
# プロビジョニングのみ再実行
vagrant provision

# 特定サーバーのみプロビジョニング
vagrant provision web-server
vagrant provision db-server
```

### カスタムドメイン管理

```bash
# ホストファイルの状態確認（Windows）
type C:\Windows\System32\drivers\etc\hosts | findstr localhost.demosns

# ホストファイルの状態確認（macOS/Linux）
cat /etc/hosts | grep localhost.demosns
```

## 🔒 セキュリティ対策

- **SQLインジェクション対策**: PDO prepared statements
- **XSS対策**: HTMLエスケープ処理
- **CSRF対策**: CSRFトークン検証
- **パスワード保護**: bcrypt暗号化
- **セッション管理**: セキュアなセッション設定

## 📈 今後の拡張可能性

### Phase 2: 機能拡張
- いいね機能
- コメント機能
- ユーザーフォロー機能
- ファイルアップロード機能

### Phase 3: インフラ拡張
- Dockerコンテナ化
- Kubernetes対応
- CI/CD パイプライン
- SSL/TLS対応

## 🐛 トラブルシューティング

### よくある問題と解決方法

#### VM起動エラー

```bash
# VirtualBox確認
VBoxManage --version

# VM強制削除後の再起動
vagrant destroy -f && vagrant up

# プラグインの再インストール
vagrant plugin uninstall vagrant-hostmanager
vagrant plugin install vagrant-hostmanager
```

#### アプリケーションアクセスエラー

```bash
# ポート確認
netstat -an | findstr :8080

# サービス状態確認
vagrant ssh web-server -c "sudo systemctl status nginx php8.1-fpm"

# カスタムドメイン確認（Windows）
type C:\Windows\System32\drivers\etc\hosts | findstr demosns
```

#### データベース接続エラー

```bash
# MySQL接続確認
vagrant ssh db-server -c "mysql -u sns_user -p -h localhost sns_db"

# MySQL プロセス確認
vagrant ssh db-server -c "sudo systemctl status mysql"
```

#### パス関連エラー（500エラー）

```bash
# プロビジョニングでパス自動修正
vagrant provision web-server

# 手動でパス確認
vagrant ssh web-server -c "ls -la /var/www/html/pages/auth/"
```

## 📚 参考資料

- [Vagrant Documentation](https://www.vagrantup.com/docs)
- [Ansible Documentation](https://docs.ansible.com/)
- [VirtualBox Manual](https://www.virtualbox.org/manual/)

## 📄 ライセンス

このプロジェクトはLT発表用のデモンストレーション目的で作成されています。
教育・学習目的での使用は自由ですが、本番環境での使用は推奨されません。

---

**作成者**: GitHub Copilot Assistant  
**作成日**: 2025年7月12日  
**更新日**: 2025年7月13日  
**バージョン**: 1.01  
**用途**: LT発表用デモアプリケーション
