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
            this.routeDpsByPk = <?php echo json_encode(route('dps.by-pk', [$idYear, $idDoc])); ?>;
            this.routeAuthorizeDps = <?php echo json_encode(route('dps.authorize-dps', [$idYear, $idDoc])); ?>;
            this.routeRejectDps = <?php echo json_encode(route('dps.reject-dps', [$idYear, $idDoc])); ?>;
            this.routeOcPending = <?php echo json_encode(route('dps.pending')); ?>;
            this.idYear = <?php echo json_encode($idYear); ?>;
            this.idDoc = <?php echo json_encode($idDoc); ?>;
            this.idExternalUser = <?php echo json_encode(\Auth::user()->external_id_n); ?>;
            this.userName = <?php echo json_encode(\Auth::user()->username); ?>;
        }
        var oServerData = new GlobalData();
    </script>
@endsection
@section('content')
    <div class="card" id="appDocument">
        {{-- <div class="card-header">
            Orden de compra
        </div> --}}
        <div class="card-body">
            {{-- <div class="grid-margin">
                b4-card
            </div> --}}
            <div class="card">
                <h5 class="card-header card-header-blue">Orden de compra (OC)</h5>
                <div class="card-body">
                    <div v-if="! isBigScreenSize()">
                        <div class="row">
                            <small class="form-text text-muted">NOTA: Los campos tienen scroll horizontal para leer el texto completo en caso de que el espacio en tu pantalla no sea suficiente.</small>
                        </div>
                        <br>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="">Comentarios al iniciarse la autorización:</label>
                                <textarea readonly type="text" class="form-control form-control-sm" aria-describedby="helpNotesId" rows="2">@{{ oDocument.oDpsHeader.notesAuth }}</textarea>
                                <small id="helpNotesId" class="text-muted">Estos son los comentarios capturados por quien inició el proceso de autorización</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-8">
                            <div class="form-group">
                                <label for="">Proveedor OC</label>
                                <input v-if="isBigScreenSize()" readonly type="text" class="form-control form-control-sm" name="provider"
                                    id="provider" aria-describedby="helpId" :value="oDocument.oDpsHeader.provider">
                                <textarea v-else readonly type="text" class="form-control form-control-sm" name="provider"
                                    id="provider" aria-describedby="helpId" :value="oDocument.oDpsHeader.provider" rows="2"></textarea>
                                <small id="helpId" class="form-text text-muted">Proveedor de la OC</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="form-group">
                                <label for="">Folio OC</label>
                                <input readonly type="text" name="folio" id="folio"
                                    class="form-control form-control-sm" :value="oDocument.oDpsHeader.dpsFolio">
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="form-group">
                                <label for="">Fecha OC</label>
                                <input readonly type="text" name="" id=""
                                    class="form-control form-control-sm"
                                    :value="formatDateNormal(oDocument.oDpsHeader.dtDoc)">
                            </div>
                        </div>
                        {{-- <div class="col-6 col-md-3">
                            <div class="form-group">
                                <label for="">Usuario OC</label>
                                <input readonly type="text" name="" id=""
                                class="form-control form-control-sm" :value="oDocument.oDpsHeader.dpsUser">
                                </div>
                                </div> --}}
                    </div>
                    <div class="row">
                        <div v-if="oDocument.oDpsHeader.exchangeRate > 1" class="col-2 col-md-2">
                            <div class="form-group">
                                <label for="">Moneda local</label>
                            </div>
                        </div>
                        <div v-if="oDocument.oDpsHeader.exchangeRate > 1" class="col-4 col-md-4">
                            <form class="form-inline">
                                <div class="form-group">
                                    <label for="">T. Cambio</label>
                                    <input readonly type="number" :value="oDocument.oDpsHeader.exchangeRate"
                                        name="exch-rate" class="form-control form-control-sm text-right ml-2">
                                </div>
                            </form>
                        </div>
                        <div
                            :class="oDocument.oDpsHeader.exchangeRate > 1 ? 'col-6 col-md-6' : 'col-12 col-md-6 offset-md-6'">
                            <div class="form-group">
                                <label for="">Moneda Documento</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div v-if="oDocument.oDpsHeader.exchangeRate > 1" class="col col-md-6">
                            <div class="form-group">
                                <label for="">Subtotal Mon. Loc.</label>
                                <input readonly type="text" name="subtotal" id="subtotal"
                                    class="form-control form-control-sm text-right"
                                    :value="formatAmount(oDocument.oDpsHeader.subTotal, 'MXN')">
                            </div>
                        </div>
                        <div
                            :class="oDocument.oDpsHeader.exchangeRate > 1 ? 'col-6 col-md-6' : 'col-12 col-md-6 offset-md-6'">
                            <div class="form-group">
                                <label for="">Subtotal OC</label>
                                <input readonly type="text" name="subtotal" id="subtotal"
                                    class="form-control form-control-sm text-right"
                                    :value="formatAmount(oDocument.oDpsHeader.subTotalCur, oDocument.oDpsHeader.currency)">
                            </div>
                        </div>
                    </div>
                    {{-- <div class="row">
                        <div class="col offset-md-6 offset-lg-6">
                            <div class="form-group">
                                <label for="">Impuestos Mon. Loc.</label>
                                <input readonly type="text" name="" id=""
                                    class="form-control form-control-sm text-right"
                                    :value="formatAmount(oDocument.oDpsHeader.taxCharged)">
                            </div>
                        </div>
                        <div class="col offset-md-6 offset-lg-6">
                            <div class="form-group">
                                <label for="">Impuestos OC</label>
                                <input readonly type="text" name="" id=""
                                    class="form-control form-control-sm text-right"
                                    :value="formatAmount(oDocument.oDpsHeader.taxCharged)">
                            </div>
                        </div>
                    </div> --}}
                    <div class="row">
                        <div v-if="oDocument.oDpsHeader.exchangeRate > 1" class="col col-md-6">
                            <div class="form-group">
                                <label for="">Total Mon. Loc.</label>
                                <input readonly type="text" name="" id=""
                                    class="form-control form-control-sm text-right"
                                    :value="formatAmount(oDocument.oDpsHeader.total, 'MXN')">
                            </div>
                        </div>
                        <div
                            :class="oDocument.oDpsHeader.exchangeRate > 1 ? 'col-6 col-md-6' : 'col-12 col-md-6 offset-md-6'">
                            <div class="form-group">
                                <label for="">Total OC</label>
                                <input readonly type="text" name="" id=""
                                    class="form-control form-control-sm text-right"
                                    :value="formatAmount(oDocument.oDpsHeader.totalCur, oDocument.oDpsHeader.currency)">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row custom-minor-row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">Notas de la OC</label>
                            <textarea readonly class="form-control form-control-sm" style="text-align: left" name="" id=""
                                rows="2">@{{ getDpsNotes() }}</textarea>
                            <small class="text-muted">Notas correspondientes a la OC</small>
                        </div>
                    </div>
                </div>
                @include('dps.modalprices')
                <hr>
                <div class="table-responsive">
                    <table class="display expandable-table dataTable no-footer custom-font-size" id="table_etys"
                        width="100%" cellspacing="0">
                        <thead>
                            <th>idYear</th>
                            <th>idDoc</th>
                            <th>idEty</th>
                            <th>Cve.</th>
                            <th>Concepto</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Precio ant.</th>
                            <th>% var.</th>
                            <th>Precio unit.</th>
                            <th>Subtotal</th>
                            <th>Impuesto trasladado</th>
                            <th>Impuesto retenido</th>
                            <th>Total</th>
                            <th>Moneda</th>
                            <th>Centro costo</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <hr>
                @include('dps.modalrm')
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <h5 class="card-header card-header-yellow">Requisición de materiales (RM)</h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label for="">Solicitante RM</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="oMaterialRequest.mrUser">
                                            <small id="helpId" class="form-text text-muted">Este es el usuario de
                                                siie que hizo la RM</small>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label for="">Folio RM</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="oMaterialRequest.mrFolio">
                                            <small id="helpId" class="form-text text-muted">Este es el número
                                                identificador de la RM</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="form-group">
                                            <label for="">Fecha captura RM</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="formatDateNormal(oMaterialRequest.mrDate)">
                                            <small id="helpId" class="form-text text-muted">Fecha en la que se
                                                realizó la RM</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="form-group">
                                            <label for="">Prioridad RM</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="oMaterialRequest.mrPriority">
                                            <small id="helpId" class="form-text text-muted">Prioridad con la que se
                                                solicita el material</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="form-group">
                                            <label for="">Fecha requerida de entrega RM</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="formatDateNormal(oMaterialRequest.mrRequiredDate)">
                                            <small id="helpId" class="form-text text-muted">Fecha límite para la
                                                entrega (por el solicitante)</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="form-group">
                                            <label for="">Tipo RM</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="oMaterialRequest.mrType === 'C' ? 'Consumo' : 'Resurtido'">
                                            <small id="helpId" class="form-text text-muted">Resurtido: proviene de
                                                almacén</small>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <div class="form-group">
                                            <label for="">Notas de la RM</label>
                                            <textarea readonly class="form-control" name="mrNotesName" id="mrNotesId" aria-describedby="helpRmEtysId" rows="2">@{{ getMrNotes() }}</textarea>
                                            <small v-if="oMaterialRequest.lEtyNotes && oMaterialRequest.lEtyNotes.length > 0" 
                                                    id="helpRmEtysId" 
                                                    class="form-text text-muted">IMPORTANTE: También hay notas en las partidas de la RM.</small>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label for="">PDF de la Requisición de materiales</label>
                                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                                data-toggle="modal" data-target="#modalRm">
                                                Ver Requisición de materiales
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('dps.modalfile')
                <div class="row">
                    <div class="col-12 col-md-4" v-for="(oFileContainer, index) in oDocument.lFiles">
                        <div class="card border-primary mb-3 with-border" style="max-width: 18rem;">
                            <div :class="'card-header' + getHeaderColor(oFileContainer.fileType)">@{{ getFileContainerHeader(oFileContainer.fileType) }}
                            </div>
                            <div class="card-body text-primary">
                                <h5 class="card-title">@{{ oFileContainer.oWebFile.externalBpName }}</h5>
                                <p class="card-notes">@{{ getFileNotes(oFileContainer) }}</p>
                                <br>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    @click="setCurrentFileContainer(oFileContainer, index)" data-toggle="modal"
                                    data-target="#modelId">
                                    Ver documento
                                </button>
                            </div>
                            <div class="card-footer bg-transparent border-success" style="text-align: right">
                                <div v-if="oFileContainer.textEtys">
                                    @{{ oFileContainer.textEtys }}
                                    <br>
                                </div>
                                @{{ 'Total archivo: ' + formatAmount(oFileContainer.totalLocal, 'MXN') }}
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
                                            <input readonly :value="oDocument.oDpsHeader.authText" type="text"
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
        'colTargets' => [0, 1, 2],
        'colTargetsSercheable' => [],
        'order' => [[3, 'asc']],
        // 'select' => true,
        'double_click' => false,
        'show' => false,
        'colTargetsNoOrder' => [],
        'colTargetsAlignRight' => [5, 7, 8, 9, 10, 11, 12, 13],
        'colTargetsAmount' => [7, 9, 10, 11, 12, 13],
        'colTargetsQuantity' => [5],
        'colTargetsNoWrap' => [3],
        // 'noSort' => true,
    ])
    <script type="text/javascript" src="{{ asset('myApp/Utils/datatablesUtils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myApp/Dps/AppDocumentVue.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#table_etys tbody').on('click', 'tr', function() {
                if (!$(this).hasClass('noSelectableRow')) {
                    if ($(this).hasClass('selected')) {
                        const selectedData = table['table_etys'].row('.selected').data();
                        documentApp.onSelectDpsEty(selectedData);
                    } else {
                        table['table_etys'].$('tr.selected').removeClass('selected');
                        $(this).addClass('selected');
                    }
                }
            });
        });
    </script>
@endsection
