<div class="modal fade" id="modal_requisition" tabindex="-1" aria-labelledby="modal_providers_form" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 70rem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_providers_form"><b> @{{ modal_title }} </b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Folio:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="folio" placeholder="Folio" v-model="folio" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Estatus autorización:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="authorizationStatusName" placeholder="Estatus autorización" v-model="authorizationStatusName" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Tipo:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="dataTypeName" placeholder="Tipo" v-model="dataTypeName" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Centro suministro:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="supplierEntity" placeholder="Proveedor" v-model="supplierEntity" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Fecha:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="date" placeholder="Fecha" v-model="date" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group sm-form-group row">
                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Usuario requisición:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="date" placeholder="Usuario requisición" v-model="usr_req" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-show="isReject">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-12 col-form-label" style="color: red;">Ingrese comentario:</label>
                                <div class="col-sm-12">
                                    <textarea rows="3" v-model="comment" style="width: 100%"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="display expandable-table dataTable no-footer" id="table_details" width="100%" cellspacing="0">
                        <thead>
                            <th>idEty</th>
                            <th>consumeEntity</th>
                            <th>subConsumeEntity</th>
                            <th>fcc</th>
                            <th style="min-width: 200px !important;">Item</th>
                            <th>Unidad</th>
                            <th>Cantidad</th>
                            <th>Precio u.</th>
                            <th>Total</th>
                            <th>C. Consumo</th>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><b>Cerrar</b></button>
                <template v-if="authorn_status != 0">
                    <button v-if="isReject" type="button" class="btn btn-info btn-icon-text" id="btn_approve" v-on:click="confirmRequestResource(0)">
                        <b>Enviar rechazo</b>
                        <i class="bx bxs-paper-plane"></i>
                    </button>
                    <button v-if="!isReject" type="button" class="btn btn-danger btn-icon-text" id="btn_approve" v-on:click="isReject = true;">
                        <b>Rechazar</b>
                        <i class="bx bxs-dislike"></i>
                    </button>
                    <button v-if="!isReject" type="button" class="btn btn-success btn-icon-text" id="btn_approve" v-on:click="confirmRequestResource(1)">
                        <b>Autorizar</b>
                        <i class="bx bxs-like"></i>
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>