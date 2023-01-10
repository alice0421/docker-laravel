<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/api.js'])
    </head>
    <body>
    <h1 class="text-3xl font-bold m-2">気象庁の天気予報WebAPI（非公式）経由で表示（東京都の詳細）</h1>
        <table>
            <tr id="publishingOffice">
                <th>発表者</th>
                <td></td>
            </tr>
            <tr id="reportDatetime">
                <th>報告日時</th>
                <td></td>
            </tr>
            <tr id="targetArea">
                <th>対象地域</th>
                <td></td>
            </tr>
            <tr id="today">
                <th>今日の天気</th>
                <td></td>
            </tr>
            <tr id="tomorrow">
                <th>明日の天気</th>
                <td></td>
            </tr>
            <tr id="dayAfterTomorrow">
                <th>明後日の天気</th>
                <td></td>
            </tr>
        </table>
    <p>出典: 気象庁ホームページ(<a href="https://www.jma.go.jp/bosai/forecast/">https://www.jma.go.jp/bosai/forecast/</a>)</p>
    </body>
</html>