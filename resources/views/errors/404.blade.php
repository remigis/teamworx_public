<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito"
          rel="stylesheet">

    <link rel="icon" href="{{ asset('storage/images/favicon.png')}}">
    <link href="{{ asset('css/app.css')}}" rel="stylesheet">
    @routes
</head>
<body>
<div id="app">

    @include('layouts.nav')

    <main class="py-4">
        <error-display></error-display>
        <div class="nav_space"></div>
        <div class="container">
            <div class="col-12 align-content-center text-center">
                <h3 class="my-5">Page not here</h3>
                <img class="max-width100" src="/storage/images/unDraw/undraw_taken_re_yn20.svg" alt="Forbidden">
            </div>
        </div>
    </main>

    @include('layouts.side-nav')
</div>

<script src="{{ asset('js/app.js')}}"></script>
<script src="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js')}}"></script>
</body>
</html>

