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

class SDpsFileContainer {
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
        idYear: oServerData.idYear,
        idDoc: oServerData.idDoc,
        oDocument: new SDocument(),
        oDocumentEty: new SDocumentEty(),
        oMaterialRequest: new SMaterialRequest(),
        oCurrentFileContainer: new SDpsFileContainer(),
        iCurrentIndex: 0,
        iNumFiles: 0,
        oWebAuthorization: new SWebAuthorization(),
        sComments: ''
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
                    console.log(this.oDocument);
                    if (this.oDocument.oWebAuthorization) {
                        this.oWebAuthorization = this.oDocument.oWebAuthorization;
                    }

                    if (this.oDocument.lEtys) {
                        for (let oEty of this.oDocument.lEtys) {
                            oEty.currency = 'MXN';
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
        getMrNotes() {
            let notes = '';
            if (!this.oMaterialRequest.lNotes) {
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
                'total',
                'currency',
                'costCenter'
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
            this.oMaterialRequest = this.oDocumentEty.oMaterialRequest;
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
        getFileContainerHeader(fileType) {
            if (fileType === 'Q') {
                return "Cotización seleccionada";
            }
            else if (fileType === 'Q+') {
                return "Cotización más cara";
            }
            else if (fileType === 'Q-') {
                return "Cotización más barata";
            }
            else if (fileType === 'T') {
                return "Ficha técnica";
            }

            return 'Archivo';
        },
        setCurrentFileContainer(fileContainer, index) {
            this.oCurrentFileContainer = fileContainer;

            SGui.showWaitingBlock(4000);
            if (!this.oCurrentFileContainer) {
                this.iCurrentIndex = 0;
                return;
            }
            this.iCurrentIndex = index;
            this.iNumFiles = this.oDocument.lFiles ? this.oDocument.lFiles.length : 0;
        },
        prevContainer() {
            if (this.oDocument.lFiles && this.oDocument.lFiles.length > 0) {
                if (this.iCurrentIndex > 0) {
                    this.iCurrentIndex--;
                    this.setCurrentFileContainer(this.oDocument.lFiles[this.iCurrentIndex], this.iCurrentIndex);
                }
                else {
                    this.iCurrentIndex = this.oDocument.lFiles.length - 1;
                    this.setCurrentFileContainer(this.oDocument.lFiles[this.iCurrentIndex], this.iCurrentIndex);
                }
            }
        },
        nextContainer() {
            if (this.oDocument.lFiles && this.oDocument.lFiles.length > 0) {
                if (this.iCurrentIndex < this.oDocument.lFiles.length - 1) {
                    this.iCurrentIndex++;
                    this.setCurrentFileContainer(this.oDocument.lFiles[this.iCurrentIndex], this.iCurrentIndex);
                }
                else {
                    this.iCurrentIndex = 0;
                    this.setCurrentFileContainer(this.oDocument.lFiles[this.iCurrentIndex], this.iCurrentIndex);
                }
            }
        },
        getFileNotes(oContainer) {
            if (!oContainer) {
                return "(Sin notas de archivo)";
            }

            if (!oContainer.notes) {
                return "(Sin notas de archivo)";
            }

            return oContainer.notes;
        },
        getPdfMaterialRequestUrl(oMatReq) {
            if (!oMatReq) {
                return "";
            }

            if (!oMatReq.mrStorageCloudUrl) {
                return "";
            }

            if (this.isBigScreenSize()) {
                return oMatReq.mrStorageCloudUrl + '#zoom=95'
            }

            const fileUrl = encodeURIComponent(oMatReq.mrStorageCloudUrl);
            const googleViewerUrl = `https://docs.google.com/viewer?embedded=true&url=${fileUrl}#zoom=100`;

            return googleViewerUrl;
        },
        getFileExtensionType(oContainer) {
            if (!oContainer) {
                return "NONE";
            }

            if (!oContainer.oWebFile.cloudFileUrl) {
                return "NONE";
            }

            // a minusculas:
            const extension = oContainer.oWebFile.fileExtension.toLowerCase();
            let extensionType = '';
            switch (extension) {
                case 'pdf':
                    extensionType = 'PDF';
                    break;
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    extensionType = 'IMAGE';
                    break;
                default:
                    extensionType = 'FILE';
                    break;
            }

            return extensionType;
        },
        getImageFileUrl(oContainer) {
            if (!oContainer) {
                return "#";
            }

            if (!oContainer.oWebFile.cloudFileUrl) {
                return "#";
            }

            return oContainer.oWebFile.cloudFileUrl;
        },
        getPdfFileUrl(oContainer) {
            if (!oContainer) {
                return "#";
            }

            if (!oContainer.oWebFile.cloudFileUrl) {
                return "#";
            }

            if (this.isBigScreenSize()) {
                return oContainer.oWebFile.cloudFileUrl + '#zoom=95'
            }

            const fileUrl = encodeURIComponent(oContainer.oWebFile.cloudFileUrl);
            const googleViewerUrl = `https://docs.google.com/viewer?embedded=true&url=${fileUrl}#zoom=100`;

            return googleViewerUrl;
        },
        getFileUrl(oContainer) {
            if (!oContainer) {
                return "#";
            }

            if (!oContainer.oWebFile.cloudFileUrl) {
                return "#";
            }

            return oContainer.oWebFile.cloudFileUrl;
        },
        isBigScreenSize() {
            const screenWidth = window.innerWidth;
            const isBig = screenWidth >= 768;
            return isBig;
        },
        /**
         * Autorizaciones
         */
        async authorize() {
            if (! this.validateAuthorization()) {
                return;
            }

            SGui.showWaiting(3000);
            await axios.post(this.oData.routeAuthorizeDps, {
                comments: this.sComments,
            })
                .then(response => {
                    console.log(response.data);
                    const sData = response.data;
                    const oData = JSON.parse(sData);
                    if (oData.code === 200) {
                        this.getDocument();
                        this.sComments = '';
                        SGui.showOkMessage('Documento autorizado');
                        // redireccionar a la vista de documentos pendientes
                        window.location.href = this.oData.routeOcPending;
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
            await axios.post(this.oData.routeRejectDps, {
                comments: this.sComments,
            })
                .then(response => {
                    console.log(response.data);
                    const sData = response.data;
                    const oData = JSON.parse(sData);
                    if (oData.code === 200) {
                        this.getDocument();
                        this.sComments = '';
                        SGui.showOkMessage('Documento rechazado');
                        // redireccionar a la vista de documentos pendientes
                        window.location.href = this.oData.routeOcPending;
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
            // validar longitud comentarios <= 255
            if (this.sComments.length > 255) {
                SGui.showError('El comentario no puede exceder los 255 caracteres');
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
                    ! this.oDocument.oDpsHeader.authorized;
        }
    },
});