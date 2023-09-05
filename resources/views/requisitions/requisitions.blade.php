@extends('layouts.principal')

@section('headJs')
<script>
    function GlobalData(){
        this.lResources = <?php echo json_encode($lResources); ?>;
        this.routeApprobe = <?php echo json_encode(route('requisitions.approbe')); ?>;
        this.routeReject = <?php echo json_encode(route('requisitions.reject')); ?>;
        this.routeSteps = <?php echo json_encode(route('requisitions.steps')); ?>;
        this.responseIndexCode = <?php echo json_encode($code); ?>;
        this.responseIndexMessage = <?php echo json_encode($message); ?>;
    }
    var oServerData = new GlobalData();
    var indexesRequisitionsTable = {
                'idResource': 0,
                'typeResource': 1,
                'folio': 2,
                'type': 3,
                'status': 4,
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
            @include('layouts.buttons', ['show' => true, 'detail' => true, 'lock' => true])
        </div>

        <div class="table-responsive">
            <table class="display expandable-table dataTable no-footer" id="table_resources" width="100%" cellspacing="0">
                <thead>
                    <th>idResource</th>
                    <th>typeResource</th>
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
                                            'colTargets' => [0, 1],
                                            'colTargetsSercheable' => [],
                                            'select' => true,
                                            'show' => true,
                                        ] )

    @include('layouts.table_jsControll', [
                                            'table_id' => 'table_steps',
                                            'colTargets' => [],
                                            'colTargetsSercheable' => [],
                                            'order' => [[3, 'asc']],
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
                'folio',
                'dataTypeName',
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
    </script>
@endsection