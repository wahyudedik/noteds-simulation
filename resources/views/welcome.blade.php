<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="refresh" content="0;url={{ url('/') }}">
        <title>{{ config('app.name', 'Noteds') }}</title>
    </head>
    <body>
        <p>Redirecting to <a href="{{ url('/') }}">{{ config('app.name', 'Noteds') }}...</a></p>
    </body>
</html>
