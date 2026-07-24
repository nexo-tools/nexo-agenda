<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>{{ isset($title) ? $title.' — '.config('app.name') : config('app.name') }}</title>

<link rel="icon" href="/favicon.ico" sizes="48x48">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#7c3aed">

@include('partials.theme-init')

@vite(['resources/css/app.css', 'resources/js/app.js'])
