<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Autorizaciones</title>
    <!-- End meta tags -->

    <!-- CSS files-->
    <link rel="stylesheet" href="{{ asset('varios/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('varios/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('varios/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('varios/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('boxicons/css/boxicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('select2js/css/select2.min.css') }}">

    <!-- Datatables CSS -->
    <link rel="stylesheet" href="{{ asset('datatables/datatables.css') }}">
    <!-- End datatables CSS -->

    <!-- CSS principal -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- End CSS principal -->

    <!-- CSS section -->
    {{-- <link  rel="stylesheet" href="{{ asset('daterange/daterangepicker.css') }}"> --}}

    @yield('headStyles')
    <!-- End CSS section -->
    <!-- End CSS files -->

    <!-- Icon browser -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />
    <!-- End icon browser -->

    <!-- Header scripts -->
    <script src="{{ asset('jquery/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vue/vue.js') }}"></script>
    <script src="{{ asset('sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/myApp/gui/SGui.js') }}"></script>
    <script src="{{ asset('moment/moment.js') }}"></script>
    <script src="{{ asset('moment/moment-with-locales.js') }}"></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('./sw.js')
                .then(function(reg) {
                    console.log('Service Worker registrado!', reg);
                })
                .catch(function(error) {
                    console.log('Error al registrar el Service Worker:', error);
                });
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
            const base64 = (base64String + padding)
                .replace(/-/g, '+')
                .replace(/_/g, '/');

            const rawData = atob(base64);
            return Uint8Array.from([...rawData].map(char => char.charCodeAt(0)));
        }

        async function subscribeUser() {
            const vapidPublicKey = "{{ env('VAPID_PUBLIC_KEY') }}";

            if ('serviceWorker' in navigator && 'PushManager' in window) {
                try {
                    const permission = await Notification.requestPermission();
                    if (permission !== 'granted') {
                        console.error('Permiso denegado para notificaciones');
                        return;
                    }
                    console.log('Permiso concedido');

                    console.log('Antes de obtener el Service Worker...');
                    const registration = await navigator.serviceWorker.ready;
                    console.log('Service Worker listo:', registration);

                    const decodedPublicKey = urlBase64ToUint8Array(vapidPublicKey);

                    const subscription = await registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: decodedPublicKey
                    });

                    console.log('Suscripción exitosa:', subscription);

                    const response = await fetch('/save-subscription', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(subscription)
                    });

                    if (!response.ok) {
                        throw new Error(`Error al guardar la suscripción: ${response.statusText}`);
                    }

                    alert('¡Te has suscrito a las notificaciones correctamente!');
                } catch (error) {
                    console.error('Error al suscribirse:', error.message);
                    alert('Hubo un problema al suscribirse. Revisa la consola para más detalles.');
                }
            } else {
                alert('Las notificaciones no son compatibles con tu navegador.');
            }
        }
    </script>
    <!-- Header scripts section -->
    @yield('headJs')
    <!-- end Header scripts section-->
    <!-- End header scripts -->

</head>

<body class="sidebar-dark">
    <!-- Page container -->
    <div class="container-scroller">

        <!-- Topbar -->
        @include('layouts.topbar')
        <!-- End Topbar -->

        <!-- Main container -->
        <div class="container-fluid page-body-wrapper">
            <!-- End Float button-->

            <!-- Right sidebar -->
            @include('layouts.right_sidebar')
            <!-- End Right sidebar -->

            <!-- Menu sidebar -->
            @include('layouts.aside')
            <!-- End Menu sidebar -->

            <!-- Panel principal-->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="loader"></div>
                    <div class="hiddeToLoad">
                        <!-- Panel content -->
                        @yield('content')
                    </div>
                    <!-- End Panel content -->
                </div>
                <!-- Footer -->
                @include('layouts.footer')
                <!-- End Footer -->
            </div>
            <!-- End Panel principal-->

        </div>
        <!-- End Main container -->

    </div>
    <!-- End Page container -->

    <!-- JS files -->
    <script src="{{ asset('varios/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('varios/chart.js/Chart.min.js') }}"></script>
    <!-- Datatables js -->
    <script src="{{ asset('datatables/datatables.js') }}"></script>
    <!-- End datatables js -->
    <script src="{{ asset('js/principal/off-canvas.js') }}"></script>
    <script src="{{ asset('js/principal/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('js/principal/template.js') }}"></script>
    <script src="{{ asset('js/principal/settings.js') }}"></script>
    <script src="{{ asset('js/principal/todolist.js') }}"></script>
    <script src="{{ asset('js/principal/Chart.roundedBarCharts.js') }}"></script>
    <script src="{{ asset('axios/axios.min.js') }}"></script>
    <script src="{{ asset('varios/select2/select2.min.js') }}"></script>
    {{-- <script src="{{ asset('daterange/daterangepicker.min.js') }}"></script> --}}
    <!-- JS section -->
    @yield('scripts')
    <!-- End JS section -->

    <script>
        window.onload = function() {

            const loader = document.querySelector('.loader');
            loader.style.opacity = 0; /* Cambia la opacidad a 0 para que el círculo desaparezca */

            var elementos = document.getElementsByClassName("hiddeToLoad");
            for (var i = 0; i < elementos.length; i++) {
                // Establecer el estilo "display" de cada elemento a "block"
                elementos[i].style.display = 'block';
            }
            loader.style.display = 'none'; /* Oculta el círculo después de una pequeña transición */

        };
    </script> 
    <!-- End JS files -->
</body>

</html>
