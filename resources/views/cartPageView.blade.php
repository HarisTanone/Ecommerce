<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce</title>
    <!-- Sertakan file CSS hasil kompilasi -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (session('success'))
        <meta name="flash-message-success" content="{{ session('success') }}">
    @endif
</head>

<body>
    <!-- Elemen ini yang akan diisi oleh Vue -->
    <div id="app">
        <router-view></router-view>

    </div>

    <!-- Sertakan file JavaScript hasil kompilasi -->
    <script src="{{ mix('js/app.js') }}"></script>
</body>

</html>
