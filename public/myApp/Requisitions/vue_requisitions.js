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
        lSteps: [],
    },
    mounted(){
        self = this;

        if(this.oData.responseIndexCode != 200){
            SGui.showMessage('', responseIndexMessage, 'error');
        }

        $('.select2-class').select2({})

        $('#status_filter').select2({
            data: self.oData.lStatus,
        }).on('select2:select', function(e) {
            
        });

        $('#type_filter').select2({
            data: self.oData.lTypes,
        }).on('select2:select', function(e) {
            
        });
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
            this.lSteps = [];
            this.comment = null;
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
            this.modal_title = "Requisición";
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
                    // drawTableJson('table_resources', self.lResources, 'idData', 'folio', 'dataTypeName', 'authorizationStatusName');
                    drawTableJson(
                        'table_resources',
                        self.lResources,
                        'idData',
                        'dataType',
                        'authorizationStatus',
                        'folio',
                        'dataTypeName',
                        'authorizationStatusName'
                    );
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

        async showSteps(data){
            this.cleanData();
            SGui.showWaiting(15000);
            await this.getSteps(data[indexesRequisitionsTable.idResource]);

            let arrSteps = [];

            for(let step of this.lSteps){
                arrSteps.push(
                    [
                        step.stepUsername,
                        (step.isAuthorized ? 'Autorizado' : ( step.isRejected ? 'Rechazado' : "Pendiente")),
                        (step.isRequired ? 'Sí' : 'No'),
                        step.level,
                        (step.isAuthorized ? step.authUsername : ''),
                        (step.isRejected ? step.rejectUsername : ''),
                        (step.isAuthorized ? step.timeAuthorized : (step.isRejected ? step.timeRejected : ''))
                    ]
                )
            }

            this.modal_title = "Requisición " + data[indexesRequisitionsTable.folio];
            drawTable('table_steps', arrSteps);
            addClassToColumn('table_steps', arrSteps.length, 6, 'nobreak');
            Swal.close();
            $('#modal_steps').modal('show');
        },

        getSteps(resource_id){
            let route = oServerData.routeSteps;

            return new Promise((resolve, reject) => 
                axios.post(route,{
                    'resource_id': resource_id
                })
                .then( result => {
                    let data = result.data;

                    if(data.success){
                        this.lSteps = data.lSteps;
                        resolve('ok');
                    }else{
                        SGui.showMessage('', data.message, data.icon);
                        reject('error');
                    }
                })
                .catch( function(error){
                    console.log(error);
                    SGui.showError(error);
                    reject('error');
                })
            );
        }
    }
});