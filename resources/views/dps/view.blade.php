@extends('layouts.principal')

@section('headJs')
    <link rel="stylesheet" href="{{ asset('css/mystyle/mystyle.css') }}">
    <script>
        function GlobalData() {
            this.routeDpsByPk = <?php echo json_encode(route('dps.by-pk', [$idYear, $idDoc])); ?>;
            this.idYear = <?php echo json_encode($idYear); ?>;
            this.idDoc = <?php echo json_encode($idDoc); ?>;
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
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th>Impuesto cargado</th>
                            <th>Impuesto retenido</th>
                            <th>Total</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <hr>
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
                                                :value="oMaterialRequest.mrType">
                                            <small id="helpId" class="form-text text-muted">Resurtido: proviene de
                                                almacén</small>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="">Notas de la requisición</label>
                                            <textarea readonly class="form-control" name="" id="" rows="2">@{{ getMrNotes() }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card border-primary mb-3" style="max-width: 18rem;">
                            <div class="card-header">Header</div>
                            <div class="card-body text-primary">
                                <h5 class="card-title">Primary card title</h5>
                                <p class="card-text">Some quick example text to build on the card title and make up the
                                    bulk of the card's content.</p>
                            </div>
                            <div class="card-footer bg-transparent border-success">Footer</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-primary mb-3" style="max-width: 18rem;">
                            <div class="card-header">Header</div>
                            <div class="card-body text-primary">
                                <h5 class="card-title">Primary card title</h5>
                                <p class="card-text">Some quick example text to build on the card title and make up the
                                    bulk of the card's content.</p>
                            </div>
                            <div class="card-footer bg-transparent border-success">Footer</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-primary mb-3" style="max-width: 18rem;">
                            <div class="card-header">Header</div>
                            <div class="card-body text-primary">
                                <h5 class="card-title">Primary card title</h5>
                                <p class="card-text">Some quick example text to build on the card title and make up the
                                    bulk of the card's content.</p>
                            </div>
                            <div class="card-footer bg-transparent border-success">Footer</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-primary mb-3" style="max-width: 18rem;">
                            <div class="card-header">Header</div>
                            <div class="card-body text-primary">
                                <h5 class="card-title">Primary card title</h5>
                                <p class="card-text">Some quick example text to build on the card title and make up the
                                    bulk of the card's content.</p>
                            </div>
                            <div class="card-footer bg-transparent border-success">Footer</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header card-header-blue">Autorización</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-inverse table-responsive">
                                            <thead class="thead-inverse">
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Comentario</th>
                                                    <th>Usuario</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td scope="row">2024-11-21</td>
                                                    <td>Este es el comentario</td>
                                                    <td>alejandra.palafox</td>
                                                </tr>
                                                <tr>
                                                    <td scope="row">2024-12-10</td>
                                                    <td>Este es otro comentario de rechazo o autorización</td>
                                                    <td>alberto.heredia</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Usuario autorización</label>
                                            <input readonly type="text" name="" id=""
                                                class="form-control form-control-sm" :value="'Alberto Heredia'"
                                                aria-describedby="helpId">
                                            <small id="helpId" class="form-text text-muted">Usuario de autorización
                                                del documento</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Comentarios</label>
                                            <textarea class="form-control" name="" id="" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="text-align: center">
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-success">Autorizar</button>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-danger">Rechazar</button>
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
