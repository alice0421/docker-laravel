# 公開レポジトリからベースイメージをインポート
FROM mysql:8.0

# タイムゾーンをAsia/Tokyoに指定
ENV TZ='Asia/Tokyo'

# MySQLの設定ファイルをコンテナ内にコピーして対応づける
COPY ./docker/mysql/my.cnf /etc/my.cnf