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
        lRows: [],
        usr_req: null,
        authorn_status: null,
    },
    mounted(){
        self = this;

        if(this.oData.responseIndexCode != 200){
            SGui.showMessage('', this.oData.responseIndexMessage, 'error');
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
            this.usr_req = null;
            this.authorn_status = null;
        },

        async showModal(data){
            this.cleanData();
            this.idResource = data[indexesRequisitionsTable.idResource];
            let oResource = this.lResources.find(({ idData }) => idData == this.idResource);
            this.folio = oResource.folio;
            this.authorizationStatusName = oResource.authorizationStatusName;
            this.authorn_status = oResource.authorizationStatus;
            this.dataTypeName = oResource.dataTypeName;
            this.dataType = oResource.dataType;
            this.consumeEntity = oResource.consumeEntity;
            this.supplierEntity = oResource.supplierEntity;
            this.date = oResource.date;
            this.usr_req = oResource.userCreator;
            this.modal_title = "Requisición " + this.folio;

            await this.getRows(this.idResource);

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
                        'fkPriority',
                        'userCreator',
                        'folio',
                        'authorizationUser',
                        'date',
                        'consumeEntity',
                        'dataTypeName',
                        'priority',
                        'authorizationStatusName',
                    );
                    SGui.showOk();
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

            if(data[indexesRequisitionsTable.statusResource] != 0){
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
            } else {
                SGui.showMessage('', 'La requisición no necesita autorización', 'warning');
            }

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
        },

        getRows(resource_id){
            SGui.showWaiting(15000);
            let route = oServerData.routeRows;
            
            return new Promise((resolve, reject) => 
                axios.post(route, {
                    'idResource': resource_id
                })
                .then( result => {
                    let data = result.data;

                    if(data.success){
                        this.lRows = data.lRows;
                        drawTableJson(
                            'table_details',
                            this.lRows,
                            'idEty',
                            'consumeEntity',
                            'subConsumeEntity',
                            'fcc',
                            'item',
                            'symbol',
                            'qty',
                            'priceUnit',
                            'total',
                            'consumeEntity',
                        );
                        let elements = [];
                        for(row of this.lRows){
                            elements.push(
                                '<button type="button" class="btn btn-info btn-icon-text" ' +
                                    'onclick="showConsumeEntity(' + 
                                        "'" + row.consumeEntity + "'," + 
                                        "'" + row.subConsumeEntity + "'," + 
                                        "'" + row.fcc + "'" + 
                                    ')">' +
                                    '<i class="bx bx-detail"></i>' +
                                '</button>'
                            );
                        }

                        renderInTable('table_details', 5, elements);
                        Swal.close();
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
            )
        }
    }
});