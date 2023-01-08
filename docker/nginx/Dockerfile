# 公開レポジトリからベースイメージをインポート
FROM nginx:1.18-alpine

# タイムゾーンをAsia/Tokyoに指定
ENV TZ='Asia/Tokyo'

# nginx config file（Nginxの設定ファイルをコンテナ内にコピーして対応づける）
COPY ./docker/nginx/*.conf /etc/nginx/conf.d/

# コンテナに入った時の作業ディレクトリを指定
WORKDIR /var/www/html