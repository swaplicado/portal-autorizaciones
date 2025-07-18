@extends('layouts.principal')

@section('headJs')
    <link rel="stylesheet" href="{{ asset('css/mystyle/mystyle.css') }}">
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

                    const response = await fetch('./save-subscription', {
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
    <script>
        function GlobalData() {
            this.routeDps = <?php echo json_encode(route('dps.dps-range')); ?>;
            this.routeDpsView = <?php echo json_encode(route('dps.view')); ?>;
            this.bUser = <?php echo json_encode($bUser); ?>;
            this.statusFilter = <?php echo json_encode($statusFilter); ?>;
            this.sessionUserName = <?php echo json_encode(\Auth::user()->username); ?>;
        }
        var oServerData = new GlobalData();
    </script>
@endsection

@section('content')
    <div class="card" id="appDps">
        <div class="card-header">
            @if($statusFilter >= 0)
                Todas las órdenes de compra (OC)
            @else
                Órdenes de compra (OC) por autorizar
            @endif
             <button class="btn" onclick="subscribeUser()" title="Suscribirse a notificaciones">
                <i class='bx bxs-bell-plus' style='color:#f5d807'></i>
            </button>
        </div>
        <div class="card-body">
            <div class="grid-margin">
                @include('layouts.buttons', ['show' => false])
                {{-- <span class="nobreak">
                    <label for="type_filter">Filtrar tipo: </label>
                    <select class="select2-class form-control" name="type_filter" id="type_filter"></select>
                </span> --}}
                @if($statusFilter >= 0)
                    <span class="nobreak">
                        <label for="status_filter">Fecha: </label>
                        <button type="button" class="btn btn-primary btn-sm" @click="prevMonth"><i
                                class='bx bxs-chevron-left bx-sm'></i></button>
                        <input type="text" readonly class="form-control-sm" :value="sMonthYear" aria-describedby="helpId"
                            placeholder="Nov 2024">
                        <button type="button" class="btn btn-primary btn-sm" @click="nextMonth"><i
                                class='bx bxs-chevron-right bx-sm'></i></button>
                    </span>
                    <span class="nobreak">
                        <label for="status_filter">Estatus autorización: </label>
                        <select class="select2-class form-control-sm" name="status_filter" id="status_filter">
                            <option value="">TODOS</option>
                            <!-- <option value="NA">NA</option> -->
                            <option value="PENDIENTE PARA MÍ">PENDIENTE PARA MÍ</option>
                            <option value="PENDIENTE PARA OTROS">PENDIENTE PARA OTROS</option>
                            <option value="EN PROCESO">EN PROCESO</option>
                            <option value="AUTORIZADO">AUTORIZADO</option>
                            <option value="RECHAZADO">RECHAZADO</option>
                            <!-- <option value="EN ENVÍO">EN ENVÍO</option> -->
                        </select>
                    </span>
                @endif
            </div>
            <div>
                @if($statusFilter >= 0)
                    <span class="nobreak">
                            <a href="{{ route('dps.pending') }}" type="button" class="btn btn-primary btn-sm ml-1" id="btn_show">Ir a mis pendientes</a>
                    </span>
                @else
                    <span class="nobreak">
                        <a href="{{ route('dps.index') }}" type="button" class="btn btn-primary btn-sm ml-1" id="btn_show">Ir a todas</a>
                    </span>
                @endif
                <p class="form-text text-muted">
                    <strong>Nota:</strong> Para ver el detalle de una orden de compra, seleccione un renglón, después: click en el
                    botón "Ver" / presione dos veces sobre el documento / click en el folio de color azul
                </p>
                <p class="form-text text-muted">
                    <i class="bx bxs-error-circle bx-xs" style="color:#fb060a"></i> Indica prioridad de autorización ALTA.
                    / 
                    <i class="bx bx-revision bx-xs" style="color:#dbcd08e6" data-toggle="tooltip" data-placement="top" title="Este documento ha sido reenviado a autorización"></i>
                    Indica documento previamente rechazado
                </p>
            </div>
            <div class="table-responsive">
                <table class="display expandable-table dataTable no-footer custom-font-size" id="table_dps" width="100%"
                    cellspacing="0">
                    <thead>
                        <th>idYear</th>
                        <th>idDoc</th>
                        <th>-</th>
                        <th>Folio OC</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>Estatus</th>
                        <th>Turno</th>
                        <th>Centro costo</th>
                        <th>Subtotal</th>
                        <th>Total</th>
                        <th>Moneda</th>
                        <th>Tipo cambio</th>
                        <th>Folio requisición</th>
                        <th>Fecha requisición</th>
                        <th>Usuario requisición</th>
                        <th>Usuario OC</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        moment.locale('es');
        $(document).ready(function() {
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    let col_status = data[6];

                    if (settings.nTable.id == 'table_dps') {
                        let sStatus = (<?php echo json_encode($statusFilter); ?>) >= 0 ? $('#status_filter').val() : "";
                        return col_status === sStatus || sStatus === "";
                    }

                    return true;
                }
            );

            $('#status_filter').change(function() {
                table['table_dps'].draw();
            });
        });

        //     idYear: 0
        //     idDoc: 1
        //     icons: 2
        //     dpsFolio: 3
        //     dt: 4
        //     provider: 5
        //     authText: 6  // tener cuidado de cambiar el index en el filtro
        //     userInTurn: 7
        //     costCenters: 8
        //     subTotal: 9
        //     total: 10
        //     currency: 11
        //     exchangeRate: 12
        //     matReqFolio: 13
        //     matReqDt: 14
        //     matReqUser: 15
        //     dpsUser: 16
    </script>
    @include('layouts.table_jsControll', [
        'table_id' => 'table_dps',
        'colTargets' => [0, 1],
        'colTargetsSercheable' => [],
        'order' => [[2, 'desc'], [3, 'asc']],
        'displayLength' => 25,
        'double_click' => true,
        'show' => true,
        'colTargetsNoOrder' => [],
        'colTargetsAlignRight' => [9, 10, 12],
        'colTargetsAmount' => [9, 10],
        'colTargetsQuantity' => [12],
        'colTargetsNoWrap' => [4, 5, 14],
        'colTargetsDateHumans' => [],
        'colTargetsDateHumansTwo' => [4, 14],
        // 'noSort' => true,
    ])
    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/Dps/AppDpsVue.js') }}"></script>
    <script>
        $('#btn_show').click(function() {
            if (table['table_dps'].row('.selected').data() == undefined) {
                SGui.showError("Debe seleccionar un renglón");
                return;
            }
            SGui.showWaitingBlock(3000);
            app.onSelectDps(table['table_dps'].row('.selected').data());
        });

        $(document).on('dblclick', '#table_dps tbody tr', function() {
            if (table['table_dps'].row(this).data() == undefined) {
                SGui.showError("Debe seleccionar un renglón");
                return;
            }
            SGui.showWaitingBlock(3000);
            app.onSelectDps(table['table_dps'].row(this).data());
        });
    </script>
@endsection
