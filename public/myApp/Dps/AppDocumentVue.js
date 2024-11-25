class SDocument {
    constructor() {
        this.oDpsHeader = {
            provider: ""
        };
    }
}

class SDocumentEty {
    constructor() { }
}

class SMaterialRequest {
    constructor() { }
}

var documentApp = new Vue({
    el: '#appDocument',
    data: {
        oData: oServerData,
        idYear: oServerData.idYear,
        idDoc: oServerData.idDoc,
        oDocument: new SDocument(),
        oDocumentEty: new SDocumentEty(),
        oMaterialRequest: new SMaterialRequest()
    },
    mounted() {
        this.getDocument();
    },
    methods: {
        async getDocument() {
            SGui.showWaitingBlock(5000);
            await axios.get(this.oData.routeDpsByPk)
                .then(response => {
                    this.oDocument = response.data; // Actualizar la lista de documentos
                })
                .catch(error => {
                    console.error('Error al obtener los documentos:', error);
                    return [];
                });
            this.drawTable();
        },
        formatAmount(amount, currency) {
            if (typeof amount !== 'number') {
                return '0.00';
            }
            let amt = amount.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
            amt = amt + ' ' + currency;
            return amt;
        },
        formatDateNormal(data) {
            if (! data) {
                return '';
            }
            const parts = data.split('-'); // Separar el formato yyyy-mm-dd
            if (parts.length === 3) {
                const year = parts[0];
                const month = String(parts[1]).padStart(2,
                    '0'); // Asegurar dos dígitos
                const day = String(parts[2]).padStart(2,
                    '0'); // Asegurar dos dígitos
                return `${day}-${month}-${year}`;
            }
            return data;
        },
        getDpsNotes() {
            let notes = '';
            if (! this.oDocument.lNotes) {
                return '';
            }
            this.oDocument.lNotes.forEach(note => {
                notes += note.note + '\n';
            });
            return notes;
        },
        getMrNotes() {
            let notes = '';
            if (! this.oMaterialRequest.lNotes) {
                return 'Estas son las notas de la requisición de materiales';
            }
            this.oMaterialRequest.lNotes.forEach(note => {
                notes += note.note + '\n';
            });
            return notes;
        },
        drawTable() {
            drawTableJson(
                'table_etys',
                this.oDocument.lEtys,
                'idYear',
                'idDoc',
                'idEty',
                'conceptKey',
                'concept',
                'quantity',
                'unitSymbol',
                'price',
                'subtotal',
                'taxCharged',
                'taxRetained',
                'total'
            );

            if (this.oDocument.lEtys) {
                let pk = [this.oDocument.lEtys[0].idYear, this.oDocument.lEtys[0].idDoc, this.oDocument.lEtys[0].idEty];
                table['table_etys'].$('tr').removeClass('selected'); // Remueve selección previa
                table['table_etys'].row(0).select(); // Marca el primer renglón
                this.onSelectDpsEty(pk);
            }
        },
        onSelectDpsEty(aData) {
            for (const oEty of this.oDocument.lEtys) {
                if (aData[0] === oEty.idYear && aData[1] === oEty.idDoc && aData[2] === oEty.idEty) {
                    this.oDocumentEty = oEty;
                    break;
                }
            }
            this.oDocumentEty = this.oDocument.lEtys[0];
            this.oMaterialRequest = this.oDocumentEty.oMaterialRequest;
        }
    },
});