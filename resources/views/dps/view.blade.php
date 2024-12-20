@extends('layouts.principal')

@section('headJs')
    <link rel="stylesheet" href="{{ asset('css/mystyle/mystyle.css') }}">
    <script>
        function GlobalData() {
            this.routeDpsByPk = <?php echo json_encode(route('dps.by-pk', [$idYear, $idDoc])); ?>;
            this.routeAuthorizeDps = <?php echo json_encode(route('dps.authorize-dps', [$idYear, $idDoc])); ?>;
            this.routeRejectDps = <?php echo json_encode(route('dps.reject-dps', [$idYear, $idDoc])); ?>;
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
            Órden de compra
        </div> --}}
        <div class="card-body">
            {{-- <div class="grid-margin">
                b4-card
            </div> --}}
            <div class="card">
                <h5 class="card-header card-header-blue">Órden de compra (OC)</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="">Notas:</label>
                                <textarea readonly type="text" class="form-control form-control-sm" aria-describedby="helpNotesId" rows="2">@{{ oDocument.oDpsHeader.notesAuth }}</textarea>
                                <small id="helpNotesId" class="text-muted">Estas son las notas que agrega el departamento de
                                    compras al enviar la OC para su autorización</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-8">
                            <div class="form-group">
                                <label for="">Proveedor OC</label>
                                <input readonly type="text" class="form-control form-control-sm" name="provider"
                                    id="provider" aria-describedby="helpId" :value="oDocument.oDpsHeader.provider">
                                <small id="helpId" class="form-text text-muted">Proveedor de la órden de compra</small>
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
                            <label for="">Notas de la órden de compra</label>
                            <textarea readonly class="form-control form-control-sm" style="text-align: left" name="" id=""
                                rows="2">@{{ getDpsNotes() }}</textarea>
                            <small class="text-muted">Notas correspondientes a la OC</small>
                        </div>
                    </div>
                </div>
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
                            <th>Precio Un.</th>
                            <th>Subtotal</th>
                            <th>Impuesto cargado</th>
                            <th>Impuesto retenido</th>
                            <th>Total</th>
                            <th>Moneda</th>
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
                            <h5 class="card-header card-header-yellow">Requisición de materiales</h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label for="">Folio Req.</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="oMaterialRequest.mrFolio">
                                            <small id="helpId" class="form-text text-muted">Este es el número
                                                identificador de la requisición</small>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label for="">Solicitante</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="oMaterialRequest.mrUser">
                                            <small id="helpId" class="form-text text-muted">Este es el usuario de
                                                siie que hizo la requisición</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="form-group">
                                            <label for="">Fecha captura</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="formatDateNormal(oMaterialRequest.mrDate)">
                                            <small id="helpId" class="form-text text-muted">Fecha en la que se
                                                realizó la requisición</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="form-group">
                                            <label for="">Prioridad</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="oMaterialRequest.mrPriority">
                                            <small id="helpId" class="form-text text-muted">Prioridad con la que se
                                                solicita el material</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="form-group">
                                            <label for="">Fecha requerida de entrega</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="formatDateNormal(oMaterialRequest.mrRequiredDate)">
                                            <small id="helpId" class="form-text text-muted">Fecha límite para la
                                                entrega (por el solicitante)</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="form-group">
                                            <label for="">Tipo Req.</label>
                                            <input readonly type="text" class="form-control form-control-sm"
                                                name="" id="" aria-describedby="helpId"
                                                :value="oMaterialRequest.mrType === 'C' ? 'Consumo' : 'Resurtido'">
                                            <small id="helpId" class="form-text text-muted">Resurtido: proviene de
                                                almacén</small>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <div class="form-group">
                                            <label for="">Notas de la requisición</label>
                                            <textarea readonly class="form-control" name="" id="" rows="2">@{{ getMrNotes() }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label for="">PDF de la Requisición</label>
                                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                                data-toggle="modal" data-target="#modalRm">
                                                Ver requisición de materiales
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
                                @{{ formatAmount(oFileContainer.totalLocal, 'MXN') }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header card-header-blue">Autorización</div>
                            <div class="card-body">
                                <div v-if="oWebAuthorization" class="row">
                                    <form class="form-inline">
                                        <div class="form-group">
                                            <label for="">Estatus autorización:</label>
                                            <input readonly :value="oWebAuthorization.authStatusName" type="text"
                                                class="form-control form-control-sm ml-1" aria-describedby="helpIdAuthSt">
                                            <small id="helpIdAuthSt" class="text-muted ml-1">(Basado en el proceso de
                                                autorización)</small>
                                        </div>
                                    </form>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-inverse table-responsive">
                                            <thead class="thead-inverse">
                                                <tr>
                                                    <th>Nivel</th>
                                                    <th>Estatus</th>
                                                    <th>Fecha autoriz.</th>
                                                    <th>Fecha rechazo</th>
                                                    <th>Usuario</th>
                                                    <th>Comentario</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="oAuthRow in oWebAuthorization.lSteps">
                                                    <td scope="row">@{{ oAuthRow.stepLevel }}</td>
                                                    <td>@{{ oAuthRow.statusName }}</td>
                                                    <td>@{{ oAuthRow.authorizedAt ? oAuthRow.authorizedAt : '(No disponible aún)' }}</td>
                                                    <td>@{{ oAuthRow.rejectedAt ? oAuthRow.rejectedAt : '(No disponible aún)' }}</td>
                                                    <td>@{{ oAuthRow.userName }}</td>
                                                    <td>@{{ oAuthRow.comments ? oAuthRow.comments : '(Sin comentarios aún)' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <hr>
                                <div v-if="isUserInTurn() && (oWebAuthorization.idAuthStatus == 2 || oWebAuthorization.idAuthStatus == 3)"
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
                                <div v-if="isUserInTurn() && (oWebAuthorization.idAuthStatus == 2 || oWebAuthorization.idAuthStatus == 3)"
                                    class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Comentarios autorización o rechazo</label>
                                            <textarea v-model="sComments" class="form-control" name="" id="" rows="2"></textarea>
                                            <small class="text-muted">Notas que verán los usuarios involucrados en el
                                                proceso de autorización.</small>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="isUserInTurn() && (oWebAuthorization.idAuthStatus == 2 || oWebAuthorization.idAuthStatus == 3)"
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
    @include('layouts.table_jsControll', [
        'table_id' => 'table_etys',
        'colTargets' => [0, 1, 2],
        'colTargetsSercheable' => [],
        // 'select' => true,
        'double_click' => false,
        'show' => false,
        'colTargetsNoOrder' => [],
        'colTargetsAlignRight' => [5, 7, 8, 9, 10, 11],
        'colTargetsAmount' => [7, 8, 9, 10, 11],
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
