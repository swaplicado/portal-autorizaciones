@extends('layouts.principal')

@section('headJs')
<script>
    function GlobalData(){
        this.lResources = <?php echo json_encode($lResources); ?>;
        this.lStatus = <?php echo json_encode($lStatus); ?>;
        this.lTypes = <?php echo json_encode($lTypes); ?>;
        this.routeApprobe = <?php echo json_encode(route('requisitions.approbe')); ?>;
        this.routeReject = <?php echo json_encode(route('requisitions.reject')); ?>;
        this.routeSteps = <?php echo json_encode(route('requisitions.steps')); ?>;
        this.routeRows = <?php echo json_encode(route('requisitions.rows')); ?>;
        this.responseIndexCode = <?php echo json_encode($code); ?>;
        this.responseIndexMessage = <?php echo json_encode($message); ?>;
    }
    var oServerData = new GlobalData();
    var indexesRequisitionsTable = {
                'idResource': 0,
                'typeResource': 1,
                'statusResource': 2,
                'idPriority': 3,
                'user_req': 4,
                'date': 5,
                'consumeEntity': 6,
                'folio': 7,
                'type': 8,
                'priority': 9,
                'status': 10,
            };

    var indexesRequisitionDetailsTable = {
                'idEty': 0,
                'consumeEntity': 1,
                'subConsumeEntity': 2,
                'fcc': 3,
                'Item': 4,
                'Unidad': 5,
                'Cantidad': 6,
                'Precio u.': 7,
                'Total': 8,
                'C. Consumo': 9,
            };
</script>
@endsection

@section('content')
  
<div class="card" id="requisitions">

    @include('requisitions.modal_requisitions')

    @include('requisitions.modal_steps')

    <div class="card-header">
        <h3>Requisiciones</h3>
    </div>
    <div class="card-body">

        <div class="grid-margin">
            @include('layouts.buttons', ['show' => true, 'lock' => true])
            <span class="nobreak">
                <label for="type_filter">Filtrar tipo: </label>
                <select class="select2-class form-control" name="type_filter" id="type_filter"></select>
            </span>
            <span class="nobreak">
                <label for="status_filter">Filtrar estatus: </label>
                <select class="select2-class form-control" name="status_filter" id="status_filter"></select>
            </span>
        </div>

        <div class="table-responsive">
            <table class="display expandable-table dataTable no-footer" id="table_resources" width="100%" cellspacing="0">
                <thead>
                    <th>idResource</th>
                    <th>typeResource</th>
                    <th>statusResource</th>
                    <th>idPriority</th>
                    <th>Usuario requisición</th>
                    <th>Fecha</th>
                    <th>Centro consumo</th>
                    <th>Folio</th>
                    <th>Tipo</th>
                    <th>Prioridad</th>
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
        $(document).ready(function () {
            $.fn.dataTable.ext.search.push(
                function( settings, data, dataIndex ) {
                    let col_type = null;
                    let col_status = null;

                    col_type = parseInt( data[indexesRequisitionsTable.typeResource] );
                    col_status = parseInt( data[indexesRequisitionsTable.statusResource] );

                    if(settings.nTable.id == 'table_resources'){
                        let iType = parseInt( $('#type_filter').val(), 10 );
                        let iStatus = parseInt( $('#status_filter').val(), 10 );
                        if(col_type == iType || iType == 0){
                            return col_status == iStatus || iStatus == 0;
                        }else{
                            return false;
                        }
                    }

                    return true;
                }
            );

            $('#type_filter').change( function() {
                table['table_resources'].draw();
            });
            
            $('#status_filter').change( function() {
                table['table_resources'].draw();
            });
        });
    </script>

    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_resources',
                                            'colTargets' => [0,8],
                                            'colTargetsSercheable' => [1,2,3],
                                            // 'select' => true,
                                            'double_click' => true,
                                            'show' => true,
                                            'colTargetsNoOrder' => [4,5,6,7,8,9,10],
                                        ] )

    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_steps',
                                            'colTargets' => [],
                                            'colTargetsSercheable' => [],
                                            'order' => [[3, 'asc']],
                                        ] )

    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_details',
                                            'colTargets' => [0,1,2,3],
                                            'colTargetsSercheable' => [],
                                            'colTargetsAlignRight' => [6,7,8],
                                            'colTargetsAlignCenter' => [9]
                                        ] )

    <script type="text/javascript" src="{{ asset('myApp/Requisitions/vue_requisitions.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            drawTableJson(
                'table_resources',
                oServerData.lResources,
                'idData',
                'dataType',
                'authorizationStatus',
                'fkPriority',
                'userCreator',
                'date',
                'consumeEntity',
                'folio',
                'dataTypeName',
                'priority',
                'authorizationStatusName'
            );
        })
    </script>
    <script>
        $('#btn_detail').click(function () {
            if(table['table_resources'].row('.selected').data() == undefined){
                SGui.showError("Debe seleccionar un renglón");
                return;
            }

            app.showDetailModal(table['table_resources'].row('.selected').data());
        });

        $('#btn_lock').click(function () {
            if(table['table_resources'].row('.selected').data() == undefined){
                SGui.showError("Debe seleccionar un renglón");
                return;
            }

            app.showSteps(table['table_resources'].row('.selected').data());
        });

        function showConsumeEntity(consumeEntity, subConsumeEntity, fcc){
            Swal.fire({
                title: "<strong>Consumos</strong>",
                html: '<ul style="text-align: left;">' +
                            '<li><b>Centro consumo:</b> ' + consumeEntity + '</li>' +
                            '<li><b>Subcentro consumo:</b> ' + subConsumeEntity + '</li>' +
                            '<li><b>Centro costo:</b> ' + fcc + '</li>'+
                        '</ul>',
                showCloseButton: true,
                focusConfirm: false,
                confirmButtonText: `
                    <i class="fa fa-thumbs-up"></i> Ok
                `,
            });
        }
    </script>
@endsection