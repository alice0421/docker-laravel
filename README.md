# **MacOSで作るDockerを用いたLaravel9+Nginx+MySQL環境構築**

# 概要
MacOS Intel Chipにおいて、DockerでLaravel9+Nginx+MySQLを用いた環境を構築する。
動くことを優先したため、説明は最低限となっている。特に[設定ファイルの作成](#設定ファイルの作成)部分は執筆者本人もあまり理解していないため要注意。

> 2023/1/8: 
> - ~~MySQLの設定が正しいかどうかは未検証なので注意。~~
>
> 2023/1/9: 
> - MySQLへ接続できることを確認。
> - phpmyadminの設定を追記。
## 各バージョン
|ツール・アプリケーション|バージョン|
|--|--|
|Docker|20.10.21|
|Docker Compose|2.13.0|
|Composer|2.5.1|
|Laravel|9.46.0|
|npm|9.2.0|
|MySQL|8.0.31|
|Git|2.30.2|

## MacOS Intel Chip
|名前|情報|
|--|--|
|コンピューター名|MacBook Pro（3-inch, 2018, Four Thunderbolt 3 Ports）|
|プロセッサ|2.7 GHz クアッドコアIntel Core i7|
|グラフィックス|Intel Iris Plus Graphics 655 1536 MB|
|メモリ|16 GB 2133 MHz LPDDR3|

## ディレクトリ構造
今回はLEMP環境で構築。
> LEMP環境とは、OSにLinux、WebサーバーにNginx、DBサーバーにMySQL、アプリケーションサーバーにPHPを用いる環境のことである。
```最終的なディレクトリ構造
docker-laravel-vue
├─ docker
│    ├─ mysql
│    │   └─ Dockerfile
│    │   └─ my.cnf
│    ├─ nginx
│    │   └─ Dockerfile
│    │   └─ default.conf
│    └─ php
│    　    └─ Dockerfile
│    　    └─ php.ini    
├─ src
│    └─  直下にLaravelのprojectを作成
│─ .env
│─ .gitignore   
└─ docker-compose.yml
```

# DockerとDocker Composeを導入する
- [【導入編】絶対に失敗しないDockerでLaravel + Vue.jsの開発環境（LEMP環境）を構築する方法〜MacOS Intel Chip対応〜](https://yutaro-blog.net/2021/04/28/docker-laravel-vuejs-1/)

## Dockerの導入
[Docker Desctop for mac](https://www.docker.com/products/docker-desktop/)をインストールする。
参考：[【Docker Desktop】Macにインストール【Monterey/M1】](https://chigusa-web.com/blog/docker-desktop-mac/)
※Intel Chip / M1で、インストールするDocker Desctopが異なるので、注意すること。

これで、DockerとDocker Composeが使えるようになる。
```DockerとDocker Composeが存在するかを確認
Dockerのバージョン確認
$ docker -v
Docker version 20.10.21, build baeda1f

Docker Composeのバージョン確認
$ docker-compose -v
Docker Compose version v2.13.0
```

# docerk-compose.ymlとDockerfileの作成
- [【前編】絶対に失敗しないDockerでLaravel + Vue.jsの開発環境（LEMP環境）を構築する方法〜MacOS Intel Chip対応〜](https://yutaro-blog.net/2021/04/29/docker-laravel-vuejs-2/)

## docker-compose.ymlの作成
### docker-compose.ymlを作成する意味
Dockerでは、複数のコンテナを管理するためにDocker Composeを用いる。
この際、Docker Composeの設定ファイルがdocker-compose.ymlとなる。ここには、Dockerイメージをビルドするための情報、ローカル環境とDockerコンテナ間の各種対応付け、Dockerコンテナ間の依存関係などが記載される。

### docker-compose.yml
```docker
version: '3.8' # composeファイル（≠docker compose）のバージョンを記載（*1）

volumes: # データを永続保存するためにdocker volumeを設定
  mysql-volume:

services:
  # phpの設定
  app: # サービス名（≠コンテナ名）を指定
    container_name: app # コンテナ名を指定（別のdocker環境がある場合、そちらとコンテナ名が被らないように注意）
    build: # ビルドするDockerfileの設定
      context: . # docker buildコマンドを実行する場所を指定（基本ルートディレクトリを指定しておけば良い（*2））
      dockerfile: ./docker/php/Dockerfile # Dockerfileがあるパスを指定
    volumes: # ローカルとdockerコンテナ間のディレクトリやファイルなどのリソースを対応付け
      - ./src/:/var/www/html # {ローカルのリソースのパス}:{dockerコンテナのリソースのパス}
    environment: # Laravelの.envに設定してもOK（今回はdockerコンテナを起動する際に設定）
      - DB_CONNECTION=mysql
      - DB_HOST=db # dockerコンテナのmysqlのサーバー名を指定
      - DB_PORT=3306 # dockerコンテナのmysqlのポート番号を指定
      - DB_DATABASE=${DB_NAME}
      - DB_USERNAME=${DB_USER}
      - DB_PASSWORD=${DB_PASSWORD}

  # nginxの設定
  web:
    container_name: nginx
    build:
      context: .
      dockerfile: ./docker/nginx/Dockerfile
    ports: # ローカルとdockerコンテナ間のポート番号の対応付けを設定
      - ${WEB_PORT}:80 # {ローカルのポート番号}:{dockerコンテナのポート番号}（ローカルは未使用のポートを指定すること、dockerはかぶっていてもOK）
    depends_on: # コンテナ間の依存関係を設定
      - app # 先に起動するコンテナのサービス名を指定（コンテナ名ではない）
    volumes:
      - ./src/:/var/www/html
  
  # mysqlの設定
  db:
    container_name: mysql
    build:
      context: .
      dockerfile: ./docker/mysql/Dockerfile
    ports:
      - ${DB_PORT}:3306
    environment:
      MYSQL_DATABASE: ${DB_NAME}
      MYSQL_USER: ${DB_USER}
      MYSQL_PASSWORD: ${DB_PASSWORD}
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
      TZ: 'Asia/Tokyo'
    volumes:
      - mysql-volume:/var/lib/mysql # {ローカルのvolumeの名前}:{dockerコンテナのDBのパス}

  # phpmyadminの設定（必要ならば）（*3）
  phpmyadmin:
    container_name: phpmyadmin
    image: phpmyadmin/phpmyadmin:5
    ports:
      - ${PMA_PORT}:80 # nginxのdockerコンテナのパスと一致させること
    depends_on:
      - db
    environment: # 自動ログイン用に設定
      PMA_USER: "${DB_USER}"
      PMA_PASSWORD: "${DB_PASSWORD}"
```
*1: 最新のComposeファイルのバージョンは[ここ](https://matsuand.github.io/docs.docker.jp.onthefly/compose/compose-file/compose-versioning/)で確認
*2: 参考：[docker-compose.ymlのbuild設定はとりあえずcontextもdockerfileも埋めとけって話](https://qiita.com/sam8helloworld/items/e7fffa9afc82aea68a7a)
*3: 参考：[Docker+Laravel9+MySQL5+phpmyadmin+Inertia.js(Vue.js)の開発環境を構築 (Windows)](https://qiita.com/backstreet/items/e7ff5b6c09408a94e5aa)

### .env
```docker
# ローカルのポート番号のため、被らないように注意
WEB_PORT=80
DB_PORT=3306
PMA_PORT=8080 # phpmyadminの設定をしたら記載

# ここはどんな値でも構わない
DB_NAME=db_name
DB_USER=db_user
DB_PASSWORD=db_password
DB_ROOT_PASSWORD=root
```
Git管理した際に個人情報が漏れてしまうため、.envファイルはgitignoreする。

### .gitignore
```.gitignore
.env
```

## Dockerfileの作成
### PHP用のDockerfile
```Dockerfile
# 公開レポジトリからベースイメージをインポート（Laravel9はphp8.0以上が必須）
FROM php:8.2.1-fpm

# COPY php.ini
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

# Composer install（マルチステージビルド）
# マルチステージビルドを使用すると、composerのバージョン管理が楽（今回は常に最新版を指定）
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# install Node.js（マルチステージビルド）
# Breeze導入にはnpmが必要
COPY --from=node:latest /usr/local/bin /usr/local/bin
COPY --from=node:latest /usr/local/lib /usr/local/lib

# パッケージ管理ツール（apt-get）の更新＆必要パッケージのインストール
RUN apt-get update \
    && apt-get -y install \
    git \
    zip \
    unzip \
    vim \
    && docker-php-ext-install pdo_mysql bcmath
    # PHP拡張モジュール（Laravelに必要で不足しているものをインストール）：pdo_mysql（PHPからMySQLへのアクセスを可能にする）、bcmath

# コンテナに入ったとき（docker-compose exec app bash）の作業ディレクトリを指定
WORKDIR /var/www/html
```
### Nginx用のDockerfile
```Dockerfile
# 公開レポジトリからベースイメージをインポート
FROM nginx:1.18-alpine

# タイムゾーンをAsia/Tokyoに指定
ENV TZ='Asia/Tokyo'

# nginx config file（Nginxの設定ファイルをコンテナ内にコピーして対応づける）
COPY ./docker/nginx/*.conf /etc/nginx/conf.d/

# コンテナに入った時の作業ディレクトリを指定
WORKDIR /var/www/html
```

### MySQL用のDockerfile
```Dockerfile
# 公開レポジトリからベースイメージをインポート
FROM mysql:8.0

# タイムゾーンをAsia/Tokyoに指定
ENV TZ='Asia/Tokyo'

# MySQLの設定ファイルをコンテナ内にコピーして対応づける
COPY ./docker/mysql/my.cnf /etc/my.cnf
```
#### M1 Macの場合
以下を参考に、MySQLのイメージをM1に対応させること。
参考：[【M1 Mac版】絶対に失敗しないDockerでLaravel + Vue.jsの開発環境（LEMP環境）を構築する方法〜MacOS M1 Chip対応〜](https://yutaro-blog.net/2021/05/25/docker-laravel-vuejs-m1/)

# 設定ファイルの作成
- [【後編】絶対に失敗しないDockerでLaravel + Vue.jsの開発環境（LEMP環境）を構築する方法〜MacOS Intel Chip対応〜](https://yutaro-blog.net/2021/04/30/docker-laravel-vuejs-3/)

## PHPの設定ファイル（php.ini）
php.iniを作成。
```php
[Date]
# タイムゾーンの設定
date.timezone = "Asia/Tokyo"

[mbstring]
# 日本語環境の設定
mbstring.internal_encoding = "UTF-8"
mbstring.language = "Japanese"
```

## Nginxの設定ファイル（default.conf）
[Laravel9の公式サイト](https://readouble.com/laravel/9.x/ja/deployment.html)を参考にdefault.confを作成。
```conf
server {
    listen 80; # ここはdockerコンテナのポート番号を指定
    
    root /var/www/html/public;

    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass app:9000; # {phpのサーバー名}:9000（TCP通信を利用。9000はphp-fpmのデフォルトの待受ポート）
        # fastcgi_pass unix:/var/run/php/php8.0-fpm.sock; # こちらはUNIXドメインソケットを利用。これを追加しただけだと502エラーが発生するので注意。(*4)
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }

    # well-knownディレクトリを除く、全隠しファイルへのアクセスを拒否
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
*4: 参考：[docker で nginx ＆ php-fpm の PHP 実行環境を構築する（TCP/UNIX domain socket）](https://www.ritolab.com/posts/220)

## MySQLの設定ファイル（my.cnf）
```sql
# log関係のものがあると、dockerのmysqlコンテナがexit(1)してしまうため、記載しない。

[mysqld]
user=mysql
character_set_server = utf8mb4
collation_server = utf8mb4_0900_ai_ci

# timezone
default-time-zone = SYSTEM

[mysql]
default-character-set = utf8mb4

[client]
default-character-set = utf8mb4
```

# イメージの構築とコンテナ起動
- [【後編】絶対に失敗しないDockerでLaravel + Vue.jsの開発環境（LEMP環境）を構築する方法〜MacOS Intel Chip対応〜](https://yutaro-blog.net/2021/04/30/docker-laravel-vuejs-3/)
## イメージの構築
docker-laeavel-vueディレクトリにいることを確認して、ビルドを行う。
```イメージの構築
$ docker compose build
```
`Successfully built…`と出たら成功。
※`Use 'docker scan' to run Snyk…`と出ても成功ではあるが、イメージの脆弱性を指摘されているため注意。

## コンテナ起動
docker-laeavel-vueディレクトリにいることを確認して、コンテナを起動する。
```コンテナ起動
$ docker compose up
もしくは
$ docker compose up -d # デーモン起動
```
全てのコンテナが起動している(STATUSがrunningとなっている)ことが確認できたら成功。
```起動しているコンテナの表示
$ docker compose ps
NAME    COMMAND                  SERVICE             STATUS              PORTS
app     "docker-php-entrypoi…"   app                 running             9000/tcp
mysql   "docker-entrypoint.s…"   db                  running             33060/tcp, 0.0.0.0:3306->3306/tcp
nginx   "/docker-entrypoint.…"   web                 running             0.0.0.0:80->80/tcp
```

# Laravel9のインストール
appコンテナに入り、Laravel9をcomposerでインストールする。
この際、appコンテナ内の/var/www/html/には.DS.Storeファイルがある（`ls -a`で.DS.Storeファイルがあるかどうかが確認できる）。このせいで/var/www/html/直下にLaravelプロジェクトを作れないため、削除するのを忘れないこと。
```
appコンテナに入る
$ docker compose exec app bash

composerがあることを確認（バージョンが表示されたらOK）
root@3d6bc8d052fe:/var/www/html# composer -V

.DS.Storeファイルを削除
root@3d6bc8d052fe:/var/www/html# rm .DS.Store

Laravel9プロジェクトを現在のディレクトリ(src)に作成
root@3d6bc8d052fe:/var/www/html# composer create-project laravel/laravel --prefer-dist . "9.*"
```
ここまでできたら、ブラウザ（Google Chrome推奨）で`localhost:80`と入力し、Laravel9のWelcome Pageが表示されたらOK。