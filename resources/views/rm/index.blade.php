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
            this.routeRm = <?php echo json_encode(route('rm.rm-range')); ?>;
            this.routeRmView = <?php echo json_encode(route('rm.view')); ?>;
            this.bUser = <?php echo json_encode($bUser); ?>;
            this.statusFilter = <?php echo json_encode($statusFilter); ?>;
            this.sessionUserName = <?php echo json_encode(\Auth::user()->username); ?>;
        }
        var oServerData = new GlobalData();
    </script>
@endsection

@section('content')
    <div class="card" id="appRm">
        <div class="card-header">
        @switch($statusFilter)
            @case(0)
                Todas las requisiciones de materiales (RM)
                @break

            @case(-1)
                Requisiciones de materiales (RM) por autorizar
                @break

            @case(-2)
                Mis requisiciones de materiales (RM)
                @break

            @default
                Estado desconocido
        @endswitch
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
                @if($statusFilter >= 0 || $statusFilter == -2)
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
                            <option value="(NO APLICA)">NO APLICA</option>
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
                            <a href="{{ route('rm.pending') }}" type="button" class="btn btn-primary btn-sm ml-1" id="btn_show">Ir a mis pendientes</a>
                    </span>
                @elseif($statusFilter == -2)
                
                @else
                    <span class="nobreak">
                        <a href="{{ route('rm.index') }}" type="button" class="btn btn-primary btn-sm ml-1" id="btn_show">Ir a todas</a>
                    </span>
                @endif
                <p class="form-text text-muted">
                    <strong>Nota:</strong> Para ver el detalle de una requisición de material, seleccione un renglón, después: click en el
                    botón "Ver" / presione dos veces sobre el documento / click en el folio de color azul.
                </p>
                <p class="form-text text-muted">
                    <i class="bx bxs-error-circle bx-xs" style="color:#fb060a"></i> Indica prioridad de autorización URGENTE.
                    / 
                    <i class="bx bx-revision bx-xs" style="color:#dbcd08e6" data-toggle="tooltip" data-placement="top" title="Este documento ha sido reenviado a autorización"></i>
                    Indica documento previamente rechazado
                </p>
            </div>
            <div class="table-responsive">
                <table class="display expandable-table dataTable no-footer custom-font-size" id="table_rm" width="100%"
                    cellspacing="0">
                    <thead>
                        <th>idMaterialRequest</th>
                        <th>-</th>
                        <th>Folio</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th>Solicitante</th>
                        <th>Estatus</th>
                        <th>Prioridad</th>
                        <th>Turno aut.</th>
                        <th>Total ml.</th>
                        <th>Naturaleza doc.</th>
                        <th>Concepto/gasto</th>
                        <th>F. requerida</th>
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
                    let col_status = data[5];
                    let statusFilter = <?php echo json_encode($statusFilter); ?>;

                    if (settings.nTable.id === 'table_rm') {
                        // Aplica el filtro si statusFilter es >= 0 o exactamente -2
                        if (statusFilter >= 0 || statusFilter === -2) {
                            let sStatus = $('#status_filter').val();
                            return col_status === sStatus || sStatus === "";
                        }
                    }

                    return true;
                }
            );

            $('#status_filter').change(function() {
                table['table_rm'].draw();
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
        'table_id' => 'table_rm',
        'colTargets' => [0],
        'colTargetsSercheable' => [],
        'order' => [[1, 'desc'], [2, 'asc']],
        'displayLength' => 25,
        'double_click' => true,
        'show' => true,
        'colTargetsNoOrder' => [],
        'colTargetsAlignRight' => [9],
        'colTargetsAmount' => [9],
        'colTargetsQuantity' => [],
        'colTargetsNoWrap' => [],
        'colTargetsDateHumans' => [],
        'colTargetsDateHumansTwo' => [4],
        // 'noSort' => true,
    ])
    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/Rm/AppRmVue.js') }}"></script>
    <script>
        $('#btn_show').click(function() {
            if (table['table_rm'].row('.selected').data() == undefined) {
                SGui.showError("Debe seleccionar un renglón");
                return;
            }
            SGui.showWaitingBlock(3000);
            app.onSelectRm(table['table_rm'].row('.selected').data());
        });

        $(document).on('dblclick', '#table_rm tbody tr', function() {
            if (table['table_rm'].row(this).data() == undefined) {
                SGui.showError("Debe seleccionar un renglón");
                return;
            }
            SGui.showWaitingBlock(3000);
            app.onSelectRm(table['table_rm'].row(this).data());
        });
    </script>
@endsection
