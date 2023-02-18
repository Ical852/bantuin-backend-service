<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>Bantuin</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('images/logo.png') }}" rel="icon" type="image/png" sizes="192x192">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}" rel="icon" type="image/png" sizes="192x192">
    <!-- Google Web Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
	<!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/auth/bootstrap.min.css') }}">
	<!-- Fontawesome CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/auth/fontawesome-all.min.css') }}">
    <!-- Flaticon CSS -->
	<link rel="stylesheet" type="text/css" href="{{ asset('css/auth/font/flaticon.css') }}">
    <!-- Custom CSS -->
	<link rel="stylesheet" type="text/css" href="{{ asset('css/auth/style.css') }}">
</head>

<body>
    
    @yield('content')

        <script src="{{ asset('js/auth/jquery-3.5.0.min.js') }}"></script>
        <script src="{{ asset('js/auth/popper.min.js') }}"></script>
        <script src="{{ asset('js/auth/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/auth/imagesloaded.pkgd.min.js') }}"></script>
        <!-- <script src="{{ asset('js/auth/config.js') }}"></script> -->
        <script src="{{ asset('js/auth/validator.min.js') }}"></script>
        <script src="{{ asset('js/auth//main.js') }}"></script>
</body>

</html>
