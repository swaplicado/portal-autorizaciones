<!-- Modal -->
<div class="modal fade modal-custom" id="modalRm" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" v-if="oMaterialRequest">
            <div :class="'modal-header'">
                <h5 class="modal-title">@{{ 'Folio requisición: ' + oMaterialRequest.mrFolio }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe v-if="isBigScreenSize() && oMaterialRequest.mrStorageCloudUrl" id="modalPdf" class="pdf-viewer"
                    :src="getPdfMaterialRequestUrl(oMaterialRequest)" title="Vista del PDF"
                    type="application/pdf"></iframe>
                <iframe v-if="!isBigScreenSize() && oMaterialRequest.mrStorageCloudUrl" id="modalPdf"
                    :src="getPdfMaterialRequestUrl(oMaterialRequest)" title="Vista del PDF" type="application/pdf"
                    style="height: 500px"></iframe>
                <h3 v-if="! oMaterialRequest.mrStorageCloudUrl">Sin archivo cargado</h3>
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
