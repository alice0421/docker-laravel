# 公開レポジトリからベースイメージをインポート（Laravel9はphp8.0以上が必須）
FROM php:8.2.1-fpm

# COPY php.ini
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

# Composer install（マルチステージビルド）
# マルチステージビルドを使用すると、composerのバージョン管理が楽（今回は常に最新版を指定）
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# install Node.js（マルチステージビルド）
# Vue.js導入に必要（Breezeにも必要）
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