@extends('layouts.principal')

@section('headJs')
    <link rel="stylesheet" href="{{ asset('css/mystyle/mystyle.css') }}">
    <script>
        function GlobalData() {
            this.routeDps = <?php echo json_encode(route('dps.dps-range')); ?>;
            this.routeDpsView = <?php echo json_encode(route('dps.view')); ?>;
            this.bUser = <?php echo json_encode($bUser); ?>;
            this.statusFilter = <?php echo json_encode($statusFilter); ?>;
        }
        var oServerData = new GlobalData();
    </script>
@endsection

@section('content')
    <div class="card" id="appDps">
        <div class="card-header">
            Órdenes de compra (OC)
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
                            <option value="NA">NA</option>
                            <option value="PENDIENTE">PENDIENTE</option>
                            <option value="EN PROCESO">EN PROCESO</option>
                            <option value="AUTORIZADO">AUTORIZADO</option>
                            <option value="RECHAZADO">RECHAZADO</option>
                            <option value="EN ENVÍO">EN ENVÍO</option>
                        </select>
                    </span>
                @endif
            </div>
            <div>
                <p class="form-text text-muted">
                    <strong>Nota:</strong> Para ver el detalle de una orden de compra, seleccione un renglón, después: click en el
                    botón "Ver" / presione dos veces sobre el documento / click en el folio de color azul.
                </p>
            </div>
            <div class="table-responsive">
                <table class="display expandable-table dataTable no-footer custom-font-size" id="table_dps" width="100%"
                    cellspacing="0">
                    <thead>
                        <th>idYear</th>
                        <th>idDoc</th>
                        <th>Fecha</th>
                        <th>Folio OC</th>
                        <th>Proveedor</th>
                        <th>Estatus</th>
                        <th>Turno</th>
                        <th>-</th>
                        <th>Centro costo</th>
                        <th>Subtotal</th>
                        <th>Total</th>
                        <th>Moneda</th>
                        <th>Tipo cambio</th>
                        <th>Folio Req.</th>
                        <th>Fecha Req.</th>
                        <th>Usuario Req.</th>
                        <th>Usuario OC.</th>
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
        //     dt: 2
        //     dpsFolio: 3
        //     provider: 4
        //     authText: 5
        //     userInTurn: 6
        //     wasReturned: 7
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
        'order' => [[3, 'asc']],
        'displayLength' => 25,
        'double_click' => true,
        'show' => true,
        'colTargetsNoOrder' => [],
        'colTargetsAlignRight' => [9, 10, 12],
        'colTargetsAmount' => [9, 10],
        'colTargetsQuantity' => [12],
        'colTargetsNoWrap' => [2, 4, 14],
        'colTargetsDateHumans' => [],
        'colTargetsDateHumansTwo' => [2,14],
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
