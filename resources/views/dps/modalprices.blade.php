<!-- Modal -->
<div class="modal fade modal-custom" id="modalPrices" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div :class="'modal-header'">
                <h4 class="modal-title">Datos de la compra anterior</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="prices_list" v-if="oDocumentEty.lItemHistory">
                    <div v-for="oItemH in oDocumentEty.lItemHistory" class="price-card">
                        <div><strong>Clave:</strong> @{{ oItemH.conceptKey }}</div>
                        <div><strong>Concepto:</strong> @{{ oItemH.concept }}</div>
                        <div><strong>Precio:</strong> @{{ formatNumber(oItemH.currentPriceUnitaryCur, 2) }}</div>
                        <div><strong>Moneda:</strong> @{{ oItemH.currencySymbol }}</div>
                        <div><strong>% Variación:</strong> @{{ formatNumber(oItemH.percentage, 3) }}</div>
                        <div><strong>Cantidad:</strong> @{{ formatNumber(oItemH.quantity, 2) }}</div>
                        <div><strong>Unidad:</strong> @{{ oItemH.unitSymbol }}</div>
                        <div><strong>Proveedor:</strong> @{{ oItemH.lastProvider }}</div>
                        <div><strong>Fecha:</strong> @{{ formatDate(oItemH.lastPurchaseDate) }}</div>
                    </div>
                </div>
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
