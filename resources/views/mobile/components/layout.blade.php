<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ $title ?? 'Smart System' }}</title>

    <link rel="stylesheet" href="{{ asset('css/common.mobile.css') }}">
    @stack('styles')

    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400;1,700&display=swap"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,700;1,400;1,700&display=swap"
          rel="stylesheet">
</head>
<body>

<div class="container">
    @yield('content')
</div>

<footer class="footer_container">
    <div class="footer_icon_list_container">
        <div class="footer_icon_list">
            <a class="footer_link active" href="#">
                <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M5.625 19.125C4.38236 19.125 3.375 18.2616 3.375 17.1964V7.55358C3.375 6.48845 4.38236 5.625 5.625 5.625H21.375C22.6177 5.625 23.625 6.48845 23.625 7.55358V17.1964C23.625 18.2616 22.6177 19.125 21.375 19.125"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.5 16.875L19.3457 23.625H7.65433L13.5 16.875Z" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <a class="footer_link" href="#">
                <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M21.375 4.5H5.625C4.38236 4.5 3.375 5.50736 3.375 6.75V10.125C3.375 11.3676 4.38236 12.375 5.625 12.375H21.375C22.6176 12.375 23.625 11.3676 23.625 10.125V6.75C23.625 5.50736 22.6176 4.5 21.375 4.5Z"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path
                        d="M21.375 15.75H5.625C4.38236 15.75 3.375 16.7574 3.375 18V21.375C3.375 22.6176 4.38236 23.625 5.625 23.625H21.375C22.6176 23.625 23.625 22.6176 23.625 21.375V18C23.625 16.7574 22.6176 15.75 21.375 15.75Z"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6.75 9V9.01" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round"/>
                    <path d="M6.75 20.25V20.26" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </a>
            <a class="footer_link" href="#">
                <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.375 14.625L13.7355 18L23.625 14.625" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3.375 18L13.7355 21.375L23.625 18" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round"/>
                    <path d="M3.375 11.0249L13.7355 7.875L23.625 11.0249L13.7355 14.625L3.375 11.0249Z"
                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <a class="footer_link" href="#">
                <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M3.375 13.5C3.375 7.90812 7.90812 3.375 13.5 3.375C19.0919 3.375 23.625 7.90812 23.625 13.5C23.625 19.0919 19.0919 23.625 13.5 23.625C11.8265 23.625 10.2477 23.219 8.8569 22.5C7.9896 22.0517 4.636 24.2941 3.9375 23.625C3.24785 22.9644 5.21089 19.3947 4.72955 18.5625C3.86805 17.0732 3.375 15.3442 3.375 13.5Z"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7.875 14.625L12.375 11.8125L14.0625 14.625L18.5625 11.8125"
                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
    </div>
</footer>
</body>
</html>
