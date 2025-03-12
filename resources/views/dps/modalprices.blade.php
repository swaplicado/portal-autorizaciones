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
                    <div v-for="oItemH in oDocumentEty.lItemHistory" class="table-responsive">
                        <table class="table table-bordered table-sm table-condensed">
                            <tbody>
                                <tr>
                                    <th>Clave</th>
                                    <td>@{{ oItemH.conceptKey }}</td>
                                </tr>
                                <tr>
                                    <th>Concepto</th>
                                    <td>@{{ oItemH.concept }}</td>
                                </tr>
                                <tr>
                                    <th>Precio un. actual</th>
                                    <td>@{{ formatNumber(oItemH.currentPriceUnitary, 2) + ' MXN' }}</td>
                                </tr>
                                <tr>
                                    <th>Precio un. anterior</th>
                                    <td>@{{ formatNumber(oItemH.priceUnitary, 2) + ' ' + oItemH.currencySymbol }}</td>
                                </tr>
                                <tr>
                                    <th>% Variación</th>
                                    <td>@{{ formatNumber(oItemH.percentage, 3) }}</td>
                                </tr>
                                <tr>
                                    <th>Cantidad</th>
                                    <td>@{{ formatNumber(oItemH.quantity, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Unidad</th>
                                    <td>@{{ oItemH.unitSymbol }}</td>
                                </tr>
                                <tr>
                                    <th>Proveedor</th>
                                    <td>@{{ oItemH.lastProvider }}</td>
                                </tr>
                                <tr>
                                    <th>Factura</th>
                                    <td>@{{ !! oItemH.numFact ? oItemH.numFact : '' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha</th>
                                    <td>@{{ formatDate(oItemH.lastPurchaseDate) }}</td>
                                </tr>
                            </tbody>
                        </table>
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
