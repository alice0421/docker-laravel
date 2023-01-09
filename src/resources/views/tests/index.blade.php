<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        @vite('resources/css/app.css')
    </head>
    <body>
        @foreach ($tests as $test)
        <h1 class="m-2 text-3xl font-bold">{{ $test->title }}</h1>
        <p class='m-2 mb-2'>{{ $test->body }}</p>
        @endforeach
    </body>
</html>