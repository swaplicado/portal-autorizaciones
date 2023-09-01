@extends('layouts.principal')

@section('headJs')
<script>
    function GlobalData(){
        this.lResources = <?php echo json_encode($lResources); ?>;
        this.routeApprobe = <?php echo json_encode(route('requisitions.approbe')); ?>;
        this.routeReject = <?php echo json_encode(route('requisitions.reject')); ?>;
    }
    var oServerData = new GlobalData();
    var indexesRequisitionsTable = {
                'idResource': 0,
                'folio': 1,
                'type': 2,
                'status': 3,
            };
</script>
@endsection

@section('content')
  
<div class="card" id="requisitions">

    @include('requisitions.modal_requisitions')

    <div class="card-header">
        <h3>Requisiciones</h3>
    </div>
    <div class="card-body">

        <div class="grid-margin">
            @include('layouts.buttons', ['show' => true, 'detail' => true])
        </div>

        <div class="table-responsive">
            <table class="display expandable-table dataTable no-footer" id="table_resources" width="100%" cellspacing="0">
                <thead>
                    <th>idResource</th>
                    <th>folio</th>
                    <th>Tipo</th>
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
    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_resources',
                                            'colTargets' => [0],
                                            'colTargetsSercheable' => [],
                                            'select' => true,
                                            'show' => true,
                                        ] )
    <script type="text/javascript" src="{{ asset('myApp/Requisitions/vue_requisitions.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            drawTableJson('table_resources', oServerData.lResources, 'idData', 'folio', 'dataTypeName', 'authorizationStatusName');
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
    </script>
@endsection