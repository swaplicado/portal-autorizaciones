var self;
var app = new Vue({
    el: '#requisitions',
    data: {
        oData: oServerData,
        lResources: oServerData.lResources,
        idResource: null,
        folio: null,
        authorizationStatusName: null,
        dataTypeName: null,
        dataType: null,
        consumeEntity: null,
        supplierEntity: null,
        date: null,
        modal_title: null,
        isReject: false,
        comment: null,
    },
    mounted(){

    },
    methods: {
        cleanData(){
            this.idResource = null;
            this.folio = null;
            this.authorizationStatusName = null;
            this.dataTypeName = null;
            this.dataType = null;
            this.consumeEntity = null;
            this.supplierEntity = null;
            this.date = null;
            this.modal_title = null;
            this.isReject = false;
        },

        showModal(data){
            this.cleanData();
            this.idResource = data[indexesRequisitionsTable.idResource];
            let oResource = this.lResources.find(({ idData }) => idData == this.idResource);
            this.folio = oResource.folio;
            this.authorizationStatusName = oResource.authorizationStatusName;
            this.dataTypeName = oResource.dataTypeName;
            this.dataType = oResource.dataType;
            this.consumeEntity = oResource.consumeEntity;
            this.supplierEntity = oResource.supplierEntity;
            this.date = oResource.date;
            this.modal_title = "Requisicion";
            $('#modal_requisition').modal('show');
        },

        showDetailModal(data){
            this.cleanData();
            this.idResource = data[indexesRequisitionsTable.idResource];
            let oResource = this.lResources.find(({ idData }) => idData == this.idResource);
        },

        confirmRequestResource(approbe){
            Swal.fire({
                title: '¿Desea ' + (approbe == 1 ? 'aprobar' : 'rechazar') + ' la requisición ' + this.folio + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.requestResource(approbe);
                }
            })
        },

        requestResource(approbe){
            SGui.showWaiting(15000);
            let route = "";
            if(approbe){
                route = this.oData.routeApprobe;
            }else{
                route = this.oData.routeReject;
            }

            axios.post(route, {
                'idResource': this.idResource,
                'dataType': this.dataType,
                'comment': this.comment
            })
            .then( result => {
                let data = result.data;
                if(data.success){
                    this.lResources = data.lResources;
                    drawTableJson('table_resources', self.lResources, 'idData', 'folio', 'dataTypeName', 'authorizationStatusName');
                    SGui.showMessage('', data.message, data.icon);
                    $('#modal_requisition').modal('hide');
                }else{
                    SGui.showMessage('', data.message, data.icon);
                }
            })
            .catch( function(error){
                console.log(error);
                SGui.showError(error);
            })
        },
    }
});