
<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>AdminLTE 3 | Starter</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])


        {{-- <link rel="stylesheet" href="dist/css/adminlte.min.css?v=3.2.0"> --}}

    <body class="hold-transition sidebar-mini">
        <div class="wrapper" id='app'>

            @include('admin.layouts.menu')

            @include('admin.layouts.sidebar')

            <div class="content-wrapper">

                <router-view></router-view>

            </div>


            <aside class="control-sidebar control-sidebar-dark">
                <div class="p-3">
                    <h5>Title</h5>
                    <p>Sidebar content</p>
                </div>
            </aside>


            @include('admin.layouts.footer')
        </div>

    </body>
</html>
