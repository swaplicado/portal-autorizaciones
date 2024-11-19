@extends('layouts.principal')

@section('headJs')
    <link rel="stylesheet" href="{{ asset('css/mystyle/mystyle.css') }}">
    <script>
        function GlobalData() {
            this.routeDps = <?php echo json_encode(route('dps.dps-range')); ?>;
        }
        var oServerData = new GlobalData();
    </script>
@endsection

@section('content')
    <div class="card" id="appDps">
        <div class="card-header">
            Órdenes de compra
        </div>
        <div class="card-body">
            <div class="grid-margin">
                @include('layouts.buttons', ['show' => true, 'lock' => true])
                {{-- <span class="nobreak">
                    <label for="type_filter">Filtrar tipo: </label>
                    <select class="select2-class form-control" name="type_filter" id="type_filter"></select>
                </span> --}}
                {{-- <span class="nobreak">
                    <label for="status_filter">Estatus autorización: </label>
                    <select class="select2-class form-control" name="status_filter" id="status_filter"></select>
                </span> --}}
                <span class="nobreak">
                    <label for="status_filter">Fecha: </label>
                    <button type="button" class="btn btn-primary btn-sm" @click="prevMonth"><i class='bx bxs-hand-left bx-sm'></i></button>
                    <input type="text" readonly class="form-control-sm" :value="sMonthYear" aria-describedby="helpId" placeholder="Nov 2024">
                    <button type="button" class="btn btn-primary btn-sm" @click="nextMonth"><i class='bx bxs-hand-right bx-sm' ></i></button>
                </span>
            </div>

            <div class="table-responsive">
                <table class="display expandable-table dataTable no-footer custom-font-size" id="table_dps" width="100%"
                    cellspacing="0">
                    <thead>
                        <th>idYear</th>
                        <th>idDoc</th>
                        <th>Fecha</th>
                        <th>Folio</th>
                        <th>Referencia</th>
                        <th>RFC</th>
                        <th>Proveedor</th>
                        <th>Subtotal</th>
                        <th>Impuesto cargado</th>
                        <th>Impuesto retenido</th>
                        <th>Total</th>
                        <th>Moneda</th>
                        <th>Tipo cambio</th>
                        <th>Folio Req.</th>
                        <th>Fecha Req.</th>
                        <th>Usuario Req.</th>
                        <th>Usuario Doc.</th>
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
    </script>
    @include('layouts.table_jsControll', [
        'table_id' => 'table_dps',
        'colTargets' => [0, 1],
        'colTargetsSercheable' => [],
        // 'select' => true,
        'double_click' => true,
        'show' => true,
        'colTargetsNoOrder' => [],
        'colTargetsAlignRight' => [7, 8, 9, 10, 12],
        'colTargetsAmount' => [7, 8, 9, 10],
        'colTargetsQuantity' => [12],
        'colTargetsNoWrap' => [2, 6, 14],
        // 'noSort' => true,
    ])
    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/Dps/AppDpsVue.js') }}"></script>
@endsection
