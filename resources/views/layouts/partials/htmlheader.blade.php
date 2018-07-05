<head>
    <meta charset="UTF-8">
    <title> Vital Gym - @yield('htmlheader_title', 'Your title here') </title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Font Awesome Icons --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700">

    {{-- Theme styles --}}
    <script src="{{ asset('js/pace.min.js') }}"></script>
    <link href="{{ asset('/css/skins/skin-black.css') }}" rel="stylesheet" type="text/css" />
    @stack('styles')
    @stack('header-scripts')
    <link href="{{ asset('/css/AdminLTE.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
</head>
