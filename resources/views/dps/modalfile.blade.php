<!-- Modal -->
<div class="modal fade modal-custom" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" v-if="oCurrentFileContainer">
            <div :class="'modal-header' + getHeaderColor(oCurrentFileContainer.fileType)">
                <h5 class="modal-title">@{{ getFileContainerHeader(oCurrentFileContainer.fileType) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-2" style="text-align: left">
                        <button type="button" class="btn btn-primary btn-sm" @click="prevContainer"><i
                                class='bx bxs-chevron-left bx-sm'></i></button>
                    </div>
                    <div class="col-8" style="text-align: center;">
                      <h3>@{{ getFileContainerHeader(oCurrentFileContainer.fileType) }}</h3>
                      <h4>@{{ "Documento " + (iCurrentIndex + 1) + " de " + iNumFiles }}</h4>
                    </div>
                    <div class="col-2" style="text-align: right">
                        <button type="button" class="btn btn-primary btn-sm" @click="nextContainer"><i
                                class='bx bxs-chevron-right bx-sm'></i></button>
                    </div>
                </div>
                <br>
                <div v-if="!isBigScreenSize()">
                    <div class="row">
                        <small class="form-text text-muted">NOTA: Los campos tienen scroll horizontal para leer el texto completo en caso de que el espacio en tu pantalla no sea suficiente.</small>
                    </div>
                    <br>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="">Proveedor</label>
                            <input readonly type="text" :value="oCurrentFileContainer.oWebFile.externalBpName"
                                class="form-control form-control-sm" name="" id=""
                                aria-describedby="helpId" placeholder="">
                            <small id="helpId" class="form-text text-muted">Fuente de la cotización</small>
                        </div>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col">
                        <div class="form-group">
                          <label for="">Subtotal cotización USD</label>
                          <input readonly type="text"
                            class="form-control form-control-sm" name="" id="" aria-describedby="helpId" placeholder="">
                          <small id="helpId" class="form-text text-muted">Subtotal moneda de documento</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                          <label for="">Subtotal cotización ML</label>
                          <input readonly type="text"
                            class="form-control form-control-sm" name="" id="" aria-describedby="helpId" placeholder="">
                          <small id="helpId" class="form-text text-muted">Subtotal moneda local (MXN)</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                          <label for="">Subtotal cotización EUR</label>
                          <input type="text"
                            class="form-control form-control-sm" name="" id="" aria-describedby="helpId" placeholder="">
                          <small id="helpId" class="form-text text-muted">Help text</small>
                        </div>
                    </div>
                </div> --}}
                <div class="row">
                    <div v-if="oDocument.oDpsHeader.currencyId != 1" class="col">
                        <div class="form-group">
                            <label for="">Total cotización (Moneda OC)</label>
                            <input readonly type="text"
                                :value="formatAmount(oCurrentFileContainer.totalDps, oDocument.oDpsHeader.currency)"
                                style="text-align: right;" class="form-control form-control-sm" name=""
                                id="" aria-describedby="helpId" placeholder="">
                            <small id="helpId" class="form-text text-muted">Total moneda de documento</small>
                        </div>
                    </div>
                    <div :class="(oDocument.oDpsHeader.currencyId == 1 && isBigScreenSize() ? 'offset-6 ' : '') + 'col'">
                        <div class="form-group">
                            <label for="">Total cotización ML</label>
                            <input readonly type="text"
                                :value="formatAmount(oCurrentFileContainer.totalLocal, 'MXN')"
                                style="text-align: right;" class="form-control form-control-sm" name=""
                                id="" aria-describedby="helpId" placeholder="">
                            <small id="helpId" class="form-text text-muted">Total moneda local (MXN)</small>
                        </div>
                    </div>
                    {{-- <div class="col">
                        <div class="form-group">
                          <label for="">Total cotización EUR</label>
                          <input type="text"
                            class="form-control form-control-sm" name="" id="" aria-describedby="helpId" placeholder="">
                          <small id="helpId" class="form-text text-muted">Help text</small>
                        </div>
                    </div> --}}
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">Notas del archivo</label>
                            <textarea readonly :value="getFileNotes(oCurrentFileContainer)" class="form-control form-control-sm card-notes" name="" id="" rows="2" aria-describedby="helpId"></textarea>
                            <small id="helpId" class="form-text text-muted">Notas Depto. compras</small>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">Nombre del archivo</label>
                            <input readonly :value="oCurrentFileContainer.oWebFile.userFileName" type="text"
                                class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <hr>
                <img v-if="getFileExtensionType(oCurrentFileContainer) === 'IMAGE'"
                    :src="getImageFileUrl(oCurrentFileContainer)" alt="Imagen"
                    style="max-width: 90%; max-height: 90%;">
                <iframe v-if="getFileExtensionType(oCurrentFileContainer) === 'PDF' && isBigScreenSize()" id="modalPdf"
                    class="pdf-viewer" :src="getPdfFileUrl(oCurrentFileContainer)" title="Vista del PDF"
                    type="application/pdf"></iframe>
                <iframe v-if="getFileExtensionType(oCurrentFileContainer) === 'PDF' && !isBigScreenSize()"
                    id="modalPdf" :src="getPdfFileUrl(oCurrentFileContainer)" title="Vista del PDF"
                    type="application/pdf" style="height: 500px"></iframe>
                <a v-if="getFileExtensionType(oCurrentFileContainer) === 'FILE'" target="_blank" class="btn btn-primary"
                    :href="getFileUrl(oCurrentFileContainer)" role="button">Descargar archivo</a>
                <h3 v-if="getFileExtensionType(oCurrentFileContainer) === 'NONE'">Sin archivo cargado</h3>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-6">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
