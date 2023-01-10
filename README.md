# **Laravel9へのTailwindCSS導入の仕方**

そもそもLaravel Breezeを入れたら勝手にTailwindCSSは導入される。
そのため、この記事は「Breezeは使わないがTailwindCSSは使いたい」人向け。

基本以下参考通りにやれば問題ない。
参考：[Install Tailwind CSS with Laravel](https://tailwindcss.com/docs/guides/laravel)

# bladeファイル
## TailwindCSSのインストール
```ターミナル
nodeがなければインストール（AWS Cloud9にはデフォルトである）
バージョンが古ければアップデートすること（*1）

TailwindCSSをnpmでインストール
$ npm install -D tailwindcss postcss autoprefixer
$ npx tailwindcss init -p
```
*1: [Cloud9 の nodejs をバージョンアップ](https://qiita.com/takiguchi-yu/items/397dd8fb88fc466c34f0)

## tailwind.config.jsにパスを追記
`tailwind.config.js`の`content`内に、TailwindCSSを適用する全テンプレートファイルのパスを記述する。
```js
/** @type {import('tailwindcss').Config} */
module.exports = {
  // content内に全テンプレートファイルのパスを記述
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
```
## CSSにTailwindCSSを追記
`./resources/css/app.css`に以下を追記。
```css
// 以下全てを追記
@tailwind base;
@tailwind components;
@tailwind utilities;
```

## TailwindCSSをBladeに適用
任意のbladeファイルのheadに`@vite('resources/css/app.css')`を追記する。
```html
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        @vite('resources/css/app.css') <!-- ここを追記し忘れないように -->
    </head>
    <body>
      <h1 class="text-3xl font-bold underline"> <!-- TailwindCSSの書き方でデザイン適用 -->
        Hello world!
      </h1>
    </body>
</html>
```
## 実際のページに反映
以下のコマンドを実行して、実際のページにTailwindCSSのデザインを反映させる。
```Terminal
開発環境に反映
$ npm run dev

本番環境に反映
$ npm run build
```
※Docker環境では、`npm run dev`は使えない（表示されるページが本番環境のため）ので注意。