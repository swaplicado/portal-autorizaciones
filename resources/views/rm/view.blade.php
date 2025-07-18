@extends('layouts.principal')
@section('headStyles')
<style>
    .myTable th{
        padding: 0.2rem;
        text-align: center;
        line-height: 1;
        white-space: nowrap;
        vertical-align: top !important;
    }
</style>
@endsection
@section('headJs')
    <link rel="stylesheet" href="{{ asset('css/mystyle/mystyle.css') }}">
    <script>
        function GlobalData() {
            this.routeRmByPk = <?php echo json_encode(route('rm.by-pk', [$idMaterialRequest])); ?>;
            this.routeRmPending = <?php echo json_encode(route('rm.pending')); ?>;
            this.routeAuthorizeRm = <?php echo json_encode(route('rm.authorize-rm', [$idMaterialRequest])); ?>;
            this.routeRejectRm = <?php echo json_encode(route('rm.reject-rm', [$idMaterialRequest])); ?>;
            this.idMaterialRequest = <?php echo json_encode($idMaterialRequest); ?>;
            this.idExternalUser = <?php echo json_encode(\Auth::user()->external_id_n); ?>;
            this.userName = <?php echo json_encode(\Auth::user()->username); ?>;
        }
        var oServerData = new GlobalData();
    </script>
@endsection
@section('content')
    <div class="card" id="appDocument">
        {{-- <div class="card-header">
            Requisición de mariales
        </div> --}}
        <div class="card-body">
            {{-- <div class="grid-margin">
                b4-card
            </div> --}}
            <div class="card">
                <h5 class="card-header card-header-blue">Requisición</h5>
                <div class="card-body">
                    <div v-if="! isBigScreenSize()">
                        <div class="row">
                            <small class="form-text text-muted">NOTA: Los campos tienen scroll horizontal para leer el texto completo en caso de que el espacio en tu pantalla no sea suficiente.</small>
                        </div>
                        <br>
                    </div>
                    <!-- <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="">Comentarios al iniciarse la autorización:</label>
                                <textarea readonly type="text" class="form-control form-control-sm" aria-describedby="helpNotesId" rows="2">@{{ oDocument.notesAuth }}</textarea>
                                <small id="helpNotesId" class="text-muted">Estos son los comentarios capturados por quien inició el proceso de autorización</small>
                            </div>
                        </div>
                    </div> -->
                </div>
                
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label for="">Solicitante</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="oDocument.mrUser">
                                            <small id="helpId" class="form-text text-muted">Este es el usuario de
                                                siie que hizo la requisición</small>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label for="">Folio</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="`${oDocument.mrProvEntity}-${oDocument.mrFolio}`">
                                            <small id="helpId" class="form-text text-muted">Este es el número
                                                identificador de la requisición</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="form-group">
                                            <label for="">Fecha</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="formatDateNormal(oDocument.mrDate)">
                                            <small id="helpId" class="form-text text-muted">Fecha en la que se
                                                realizó la requisición</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="form-group">
                                            <label for="">Prioridad</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="oDocument.mrPriority">
                                            <small id="helpId" class="form-text text-muted">Prioridad con la que se
                                                solicita el material</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="form-group">
                                            <label for="">Fecha requerida</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="formatDateNormal(oDocument.mrRequiredDate)">
                                            <small id="helpId" class="form-text text-muted">Fecha límite para la
                                                entrega (por el solicitante)</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="form-group">
                                            <label for="">Tipo</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="oDocument.mrType === 'C' ? 'Consumo' : 'Resurtido'">
                                            <small id="helpId" class="form-text text-muted">Resurtido: proviene de
                                                almacén</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="form-group">
                                            <label for="">Concepto/gasto</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="oDocument.mrItemReference">
                                            <small id="helpId" class="form-text text-muted"></small>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <div class="form-group">
                                            <label for="">Notas</label>
                                            <textarea readonly class="form-control" name="mrNotesName" id="mrNotesId" aria-describedby="helpRmEtysId" rows="2">@{{ oDocument.lNotes.map(n => n.note).join(' | ') }}</textarea>
                                            <small v-if="oDocument.lEtyNotes && oDocument.lEtyNotes.length > 0" 
                                                    id="helpRmEtysId" 
                                                    class="form-text text-muted">IMPORTANTE: También hay notas en las partidas de la requisición.</small>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <div class="form-group">
                                            <label for="">Centros de costos</label>
                                            <!-- Si solo hay uno: input -->
                                            <input v-if="oDocument.costCenterCount === 1"
                                                readonly type="text"
                                                class="form-control form-control-sm"
                                                :value="oDocument.costCenter"
                                                :title="oDocument.costCenter"
                                                style="white-space: nowrap; overflow-x: auto;"
                                                @click="$event.target.select()" />

                                            <!-- Si hay más de uno: textarea -->
                                            <textarea v-else
                                                readonly rows="3"
                                                class="form-control form-control-sm"
                                                :value="oDocument.costCenter"
                                                :title="oDocument.costCenter"
                                                style="resize: none;"
                                                @click="$event.target.select()"></textarea>

                                            <small id="helpId" class="form-text text-muted"></small>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="table-responsive">
                                    <table class="display expandable-table dataTable no-footer custom-font-size" id="table_etys"
                                        width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>idMaterialRequest</th>
                                                <th>idEty</th>
                                                <th>Cve.</th>
                                                <th>Concepto</th>
                                                <th>Cantidad</th>
                                                <th>Precio unitario</th>
                                                <th>Precio unitario de sistema</th>
                                                <th>Total</th>
                                                <th>Centro costo</th>
                                                <th>Concepto/gasto</th>
                                                <th>Notas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header card-header-blue">Proceso de autorización</div>
                            <div class="card-body">
                                <div v-if="oWebAuthorization" class="row">
                                    <form class="form-inline">
                                        <div class="form-group">
                                            <label for="">Estatus actual:</label>
                                            <input readonly :value="oDocument.authText" type="text"
                                                class="form-control form-control-sm ml-1" 
                                                style="border: black 2px solid; font-weight: bold;">
                                            <button type="button" class="btn btn-primary btn-sm ml-1" v-on:click="bShowHistory = !bShowHistory">
                                                @{{ bShowHistory ? 'Ocultar' : 'Ver' }} anteriores
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-inverse myTable" style="font-size: 0.7em; width: 100%;">
                                            <thead class="thead-inverse" style="height: 3rem">
                                                <tr>
                                                    <th>Niv.</th>
                                                    <th style="width: 20%">Usuario</th>
                                                    <th style="width: 20%">Estatus</th>
                                                    <th style="width: 40%">Comentarios</th>
                                                    <th>Vigente</th>
                                                </tr>
                                            </thead>
                                            <tbody v-if="bShowHistory && oWebAuthorization.lSteps.filter(row => row.deleted === true).length > 0">
                                                <tr v-for="oAuthRow, index in oWebAuthorization.lSteps.filter(row => row.deleted === true)"
                                                    style="color: gray; font-style: italic;">
                                                    <td scope="row" style="padding: 0.5rem; text-align: center;">@{{ oAuthRow.stepLevel }}</td>
                                                    <td style="padding: 0.5rem; text-align: center;">@{{ oAuthRow.userName }}</td>
                                                    <td style="padding: 0.5rem; text-align: center;">
                                                        @{{ oAuthRow.statusName }}
                                                        <p>@{{ oAuthRow.authorizedAt ? oAuthRow.authorizedAt : ( oAuthRow.rejectedAt ? oAuthRow.rejectedAt : '(No disponible)' ) }}</p>
                                                    </td>
                                                    <td class="long-text"  style="padding: 0.5rem; text-align: center;">
                                                        <span v-if="!oAuthRow.showFullComment">
                                                            @{{ oAuthRow.comments ? (oAuthRow.comments.length > 100 ? (oAuthRow.comments.substring(0, 100) + '...') : oAuthRow.comments) : '(Sin comentarios)' }}
                                                        </span>
                                                        <span v-else>
                                                            @{{ oAuthRow.comments }}
                                                        </span>
                                                        <button class="btn btn-info btn-xs" v-if="oAuthRow.comments && oAuthRow.comments.length > 100" 
                                                                @click="onShowMoreComments(getRealIndex(oAuthRow), oAuthRow.showFullComment)">
                                                            @{{ oAuthRow.showFullComment ? 'Ver menos' : 'Ver más' }}
                                                        </button>
                                                    </td>
                                                    <td style="padding: 0.5rem; text-align: center;">@{{ oAuthRow.deleted === false ? 'Sí' : 'No' }}</td>
                                                </tr>
                                            </tbody>
                                            <hr>
                                            <tbody>
                                                <tr v-for="(oAuthRow, index) in oWebAuthorization.lSteps.filter(row => row.deleted === false)" :key="oAuthRow.id">
                                                    <td scope="row"  style="padding: 0.5rem;">@{{ oAuthRow.stepLevel }}</td>
                                                    <td style="padding: 0.5rem; text-align: center;"><b>@{{ oAuthRow.userName }}</b></td>
                                                    <td style="padding: 0.5rem; text-align: center;">
                                                        <b>@{{ oAuthRow.statusName }}</b>
                                                        <p><b>@{{ oAuthRow.authorizedAt ? oAuthRow.authorizedAt : ( oAuthRow.rejectedAt ? oAuthRow.rejectedAt : '(No disponible)' ) }}</b></p>
                                                    </td>
                                                    <td class="long-text" style="padding: 0.5rem; text-align: center;">
                                                        <span v-if="!oAuthRow.showFullComment">
                                                            @{{ oAuthRow.comments ? (oAuthRow.comments.length > 100 ? (oAuthRow.comments.substring(0, 100) + '...') : oAuthRow.comments) : '(Sin comentarios)' }}
                                                        </span>
                                                        <span v-else>
                                                            @{{ oAuthRow.comments }}
                                                        </span>
                                                        <button class="btn btn-info btn-xs" v-if="oAuthRow.comments && oAuthRow.comments.length > 100" 
                                                                @click="onShowMoreComments(getRealIndex(oAuthRow), oAuthRow.showFullComment)">
                                                            @{{ oAuthRow.showFullComment ? 'Ver menos' : 'Ver más' }}
                                                        </button>
                                                    </td style="padding: 0.5rem; text-align: center;">
                                                    <td style="padding: 0.5rem; text-align: center;">@{{ oAuthRow.deleted === false ? 'Sí' : 'No' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <hr>
                                <div v-if="showAuthorization()"
                                    class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Usuario autorización</label>
                                            <input readonly type="text" name="" id=""
                                                class="form-control form-control-sm" :value="oData.userName"
                                                aria-describedby="helpId">
                                            <small id="helpId" class="form-text text-muted">Usuario de autorización
                                                del documento</small>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="showAuthorization()"
                                    class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Comentarios autorización o rechazo</label>
                                            <textarea v-model="sComments" class="form-control" name="" id="" rows="2" maxlength="1022"></textarea>
                                            <small class="text-muted">Texto que verán los usuarios involucrados en el
                                                proceso de autorización.</small>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="showAuthorization()"
                                    class="row" style="text-align: center">
                                    <div class="col-6">
                                        <button type="button" @click="authorize()"
                                            class="btn btn-success">Autorizar</button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" @click="reject()" class="btn btn-danger">Rechazar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // idYear
        // idDoc
        // idEty
        // Cve. 3
        // Concepto 4
        // Cantidad 5
        // Unidad 6
        // Precio Ant. 7
        // % Dif. 8
        // Precio Un. 9
        // Subtotal 10
        // Impuesto cargado 11
        // Impuesto retenido 12
        // Total 13
        // Moneda 14
        // Centro costo 15
    </script>
    @include('layouts.table_jsControll', [
        'table_id' => 'table_etys',
        'colTargets' => [0,1],
        'colTargetsSercheable' => [],
        'order' => [[3, 'asc']],
        'select' => true,
        //'double_click' => false,
        'show' => false,
        'colTargetsNoOrder' => [],
        'colTargetsAlignRight' => [4,5,6,7],
        'colTargetsAmount' => [5,6,7],
        'colTargetsQuantity' => [],
        'colTargetsNoWrap' => [],
        // 'noSort' => true,
    ])
    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/Rm/AppDocumentVue.js') }}"></script>
    <script>
        $(document).ready(function() {
            
        });
    </script>
@endsection