class SMaterialRequest {
    constructor() { 
        this.lNotes = []
    }
}

class SMaterialRequestEty {
    constructor() { }
}

class SRmFileContainer {
    constructor() {
        this.oWebFile = {
            fileExtension: ''
        };
    }
}

class SWebAuthorization {
    constructor() {
        this.authStatusName = 'NA';
        this.idAuthStatus = 0;
        this.lastActionAt = 'NA';
        this.lSteps = [];
    }
}

var documentApp = new Vue({
    el: '#appDocument',
    data: {
        oData: oServerData,
        idMaterialRequest: oServerData.idMaterialRequest,
        oDocument: new SMaterialRequest(),
        oDocumentEty: new SMaterialRequestEty(),
        oCurrentFileContainer: new SRmFileContainer(),
        iCurrentIndex: 0,
        iNumFiles: 0,
        oWebAuthorization: new SWebAuthorization(),
        sComments: '',
        bShowHistory: false,
    },
    mounted() {
        this.getDocument();
    },
    methods: {
        async getDocument() {
            SGui.showWaitingBlock(5000);
            await axios.get(this.oData.routeRmByPk)
                .then(response => {
                    this.oDocument = response.data; // Actualizar la lista de documentos
                    console.log(this.oDocument);
                    if (this.oDocument.oWebAuthorization) {
                        this.oWebAuthorization = this.oDocument.oWebAuthorization;
                        for (let element of this.oWebAuthorization.lSteps) {
                            element.showFullComment = false;
                        }
                    }
                })
                .catch(error => {
                    SGui.showError('Error al obtener los documentos' + error + '. Contacta a soporte técnico.');
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
        formatNumber(amount, decimals) {
            if (typeof amount !== 'number') {
                return '0.00';
            }
            let amt = amount.toLocaleString('es-MX', { minimumFractionDigits: decimals, maximumFractionDigits: decimals })

            return amt;
        },
        formatDateNormal(data) {
            if (!data) {
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
        formatDate(data) {
            if (!data) {
                return '';
            }
            
            const parts = data.split('-'); // Separar el formato yyyy-mm-dd

            if (parts.length === 3) {
                const year = parts[0];
                const month = String(parts[1]).padStart(2, '0'); // Asegurar dos dígitos
                const day = String(parts[2]).padStart(2, '0'); // Asegurar dos dígitos

                return `${day}/${month}/${year}`;
            }

            return '';
        },
        getDpsNotes() {
            let notes = '';
            if (!this.oDocument.lNotes) {
                return '';
            }
            this.oDocument.lNotes.forEach(note => {
                notes += note.note + '\n';
            });
            return notes;
        },
        drawTable() {
            if (this.oDocument.lEtys) {
                for (let oEty of this.oDocument.lEtys) {
                    oEty.lEtyNotes = oEty.lEtyNotes.map(n => n.note).join(' | ');    
                }
            }

            drawTableJson(
                'table_etys',
                this.oDocument.lEtys,
                'idMaterialRequest',
                'idEty',
                'itemKey',
                'itemName',
                'quantity',
                'priceUnitary',
                'priceUnitarySystem',
                'total',
                'lEtyNotes',
            );

            if (this.oDocument.lEtys) {
                let pk = [this.oDocument.lEtys[0].idMaterialRequest, this.oDocument.lEtys[0].idEty];
                this.onSelectDpsEty(pk);
            }
        },
        onSelectDpsEty(aData) {
            for (const oEty of this.oDocument.lEtys) {
                if (aData[0] === oEty.idMaterialRequest && aData[1] === oEty.idEty) {
                    this.oDocumentEty = oEty;
                    break;
                }
            }
        },
        getHeaderColor(fileType) {
            if (fileType === 'Q') {
                return " header-selected";
            }
            else if (fileType === 'Q+') {
                return " header-high-cost";
            }
            else if (fileType === 'Q-') {
                return " header-low-cost";
            }
            else if (fileType === 'T') {
                return " header-sheet";
            }

            return '';
        },
        isBigScreenSize() {
            const screenWidth = window.innerWidth;
            const isBig = screenWidth >= 768;
            return isBig;
        },
        escapeString(str) {
            return str
                .replace(/'/g, "\\'") // Escapa comilla simple
                .replace(/\\/g, "\\\\")  // Escapa barras invertidas
                .replace(/"/g, '\\"')     // Escapa comillas dobles
                .replace(/\n/g, "\\n")    // Escapa saltos de línea
                .replace(/\r/g, "\\r")    // Escapa retornos de carro
                .replace(/\t/g, "\\t");  // Escapa tabulaciones
        },
        /**
         * Autorizaciones
         */
        async authorize() {
            if (! this.validateAuthorization()) {
                return;
            }

            SGui.showWaiting(3000);
            let jComments = this.escapeString(this.sComments);
            await axios.post(this.oData.routeAuthorizeRm, {
                comments: jComments,
            })
                .then(response => {
                    console.log(response.data);
                    const oData = response.data;
                    if (oData.code === 200) {
                        this.getDocument();
                        this.sComments = '';
                        SGui.showOkMessage('Documento autorizado');
                        // redireccionar a la vista de documentos pendientes
                        window.location.href = this.oData.routeRmPending;
                    }
                    else {
                        SGui.showError(oData.message);
                    }
                })
                .catch(error => {
                    console.error('Error al autorizar:', error);
                    return [];
                });
        },
        async reject() {
            if (!this.sComments) {
                SGui.showError('Debes escribir un comentario');
                return;
            }

            if (! this.validateAuthorization()) {
                return;
            }

            SGui.showWaiting(3000);
            let jComments = this.escapeString(this.sComments);
            await axios.post(this.oData.routeRejectRm, {
                comments: jComments,
            })
                .then(response => {
                    console.log(response.data);
                    const oData = response.data;
                    if (oData.code === 200) {
                        this.getDocument();
                        this.sComments = '';
                        SGui.showOkMessage('Documento rechazado');
                        // redireccionar a la vista de documentos pendientes
                        window.location.href = this.oData.routeRmPending;
                    }
                    else {
                        SGui.showError(oData.message);
                    }
                })
                .catch(error => {
                    console.error('Error al rechazar:', error);
                    return [];
                });
        },
        validateAuthorization() {
            // validar longitud comentarios <= 1022
            if (this.sComments.length > 1022) {
                SGui.showError('El comentario no puede exceder los 1022 caracteres');
                return false;
            }

            return true;
        },
        isUserInTurn() {
            if (! this.oData.idExternalUser) {
                return false;
            }
            if (! this.oWebAuthorization) {
                return false;
            }
            if (! this.oWebAuthorization.lUsersInTurn) {
                return false;
            }
            const lUsersInTurn = this.oWebAuthorization.lUsersInTurn;
            const idUser = this.oData.idExternalUser;
            const bTurn = lUsersInTurn.includes(idUser);

            return bTurn;
        },
        showAuthorization() {
            return this.isUserInTurn() && 
                    (this.oWebAuthorization.idAuthStatus == 2 || this.oWebAuthorization.idAuthStatus == 3) &&
                    ! this.oDocument.authorized;
        },
        getRealIndex(oAuthRow) {
            return this.oWebAuthorization.lSteps.findIndex(row => row === oAuthRow);
        },
        onShowMoreComments(iIndex, bShow) {
            if (this.oWebAuthorization.lSteps[iIndex]) {
                this.oWebAuthorization.lSteps[iIndex].showFullComment = !bShow;
                let sComment = this.oWebAuthorization.lSteps[iIndex].comments;
                this.oWebAuthorization.lSteps[iIndex].comments = '...';
                this.oWebAuthorization.lSteps[iIndex].comments = sComment;
            }
        }
    },
});