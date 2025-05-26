<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thư viện</title>
    @vite('resources/css/app.css')
    <link rel="shortcut icon" href="{{ asset('images/logoVLUTE.png') }}" />

</head>

<body class="hold-transition sidebar-mini">
    <div id="app" class="wrapper">

    </div>
    <script>
    window.userRole = "{{ session('Quyen') }}";
    window.Laravel = {
        error: "{{ session('error') }}",
        user: {
            isLogin: "{{ session('IsLogin') }}",
            email: "{{ session('Email') }}",
            username: "{{ session('Username') }}",
            hoTen: "{{ session('HoTen') }}",
            mssv: "{{ session('MSSV') }}",
            idDocGia: "{{ session('IdDocGia') }}"
        }
    };
    </script>
    @vite('resources/js/app.js')
</body>

</html>