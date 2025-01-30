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
            Ordenes de compra
        </div>
        <div class="card-body">
            <div class="grid-margin">
                @include('layouts.buttons', ['show' => false])
                {{-- <span class="nobreak">
                    <label for="type_filter">Filtrar tipo: </label>
                    <select class="select2-class form-control" name="type_filter" id="type_filter"></select>
                </span> --}}
                {{-- <span class="nobreak">
                    <label for="status_filter">Estatus autorización: </label>
                    <select class="select2-class form-control" name="status_filter" id="status_filter"></select>
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
                @endif
            </div>
            <div>
                <p class="form-text text-muted">
                    <strong>Nota:</strong> Para ver el detalle de una orden de compra, seleccione un renglón, después: de clic en el
                    botón "Ver" ó presione dos veces sobre la OC.
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
                        <th>Subtotal</th>
                        <th>Total</th>
                        <th>Moneda</th>
                        <th>Tipo cambio</th>
                        <th>Folio Req.</th>
                        <th>Fecha Req.</th>
                        <th>Usuario Req.</th>
                        <th>Usuario OC.</th>
                        <th>Estatus</th>
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
                    let col_type = null;
                    let col_status = null;

                    // col_type = parseInt(data[indexesRequisitionsTable.typeResource]);
                    // col_status = parseInt(data[indexesRequisitionsTable.statusResource]);

                    // if (settings.nTable.id == 'table_resources') {
                    //     // let iType = parseInt( $('#type_filter').val(), 10 );
                    //     let iType = 1;
                    //     let iStatus = parseInt($('#status_filter').val(), 10);
                    //     if (col_type == iType || iType == 0) {
                    //         return col_status == iStatus || iStatus == 0;
                    //     } else {
                    //         return false;
                    //     }
                    // }

                    return true;
                }
            );

            // $('#type_filter').change( function() {
            //     table['table_resources'].draw();
            // });

            $('#status_filter').change(function() {
                table['table_dps'].draw();
            });
        });

        //     idYear: 0,
        //     idDoc: 1,
        //     dt: 2,
        //     dpsFolio: 3,
        //     dpsNumRef: 4,
        //     providerFiscalId: 5,
        //     provider: 6,
        //     subTotal: 7,
        //     taxCharged: 8,
        //     taxRetained: 9,
        //     total: 10,
        //     currency: 11,
        //     exchangeRate: 12,
        //     matReqFolio: 13,
        //     matReqDt: 14,
        //     dpsUser: 15,
        //     matReqUser: 16
        //     authText: 17
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
        'colTargetsAlignRight' => [5, 6, 8],
        'colTargetsAmount' => [5, 6],
        'colTargetsQuantity' => [8],
        'colTargetsNoWrap' => [2, 4, 10],
        'colTargetsDateHumans' => [2, 10],
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
