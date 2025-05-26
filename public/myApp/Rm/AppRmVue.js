// import axios from 'axios'; // Importación correcta de Axios

var app = new Vue({
    el: '#appRm',
    data: {
        oData: oServerData,
        currentDate: new Date(), // Fecha actual
        startDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1), // Primer día del mes actual
        endDate: new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0), // Último día del mes actual
        sMonthYear: new Date().toLocaleString('default', { month: 'short', year: 'numeric' }).replace(/^\w/, c => c.toUpperCase()), // Mes y año como texto
        vDocuments: [] // Lista para almacenar los documentos obtenidos
    },
    methods: {
        prevMonth() {
            // Clonar la fecha actual y restar un mes
            const prevDate = new Date(this.currentDate);
            prevDate.setMonth(prevDate.getMonth() - 1);
            this.updateDates(prevDate);
        },
        nextMonth() {
            // Clonar la fecha actual y agregar un mes
            const nextDate = new Date(this.currentDate);
            nextDate.setMonth(nextDate.getMonth() + 1);
            this.updateDates(nextDate);
        },
        updateDates(newDate) {
            // Actualizar las fechas y obtener documentos
            this.currentDate = newDate;
            this.sMonthYear = this.currentDate.toLocaleString('default', { month: 'short', year: 'numeric' }).replace(/^\w/, c => c.toUpperCase());
            this.startDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1);
            this.endDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0);
            this.getDocuments(this.startDate, this.endDate);
        },
        async getDocuments(startDate, endDate) {
            SGui.showWaitingBlock(5000);
            // Petición GET con Axios y parámetros
            await axios.get(this.oData.routeRm, {
                params: {
                    firstDay: startDate.toISOString().split('T')[0], // Formato YYYY-MM-DD
                    lastDay: endDate.toISOString().split('T')[0],
                    bUser: this.oData.bUser,
                    statusFilter: this.oData.statusFilter
                }
            })
            .then(response => {
                this.vDocuments = response.data; // Actualizar la lista de documentos

                for (let element of this.vDocuments) {
                    element.icons = '';
                    if (element.mrPriority === 'URGENTE') {
                        element.icons += '<span data-order="2" class="order-value d-none">2</span>' +
                                        '<i class="bx bxs-error-circle bx-xs" style="color:#fb060a"' + 
                                                'data-toggle="tooltip" ' +
                                                'data-placement="top" ' +
                                                'title="Este documento tiene prioridad alta"></i>';
                    }
                    if (element.returned) {
                        element.icons += '<span data-order="1" class="order-value d-none">1</span>' +
                                        '<i class="bx bx-revision bx-xs" ' +
                                                    'style="color:rgba(171, 160, 14, 0.9)" ' +
                                                    'data-toggle="tooltip" ' +
                                                    'data-placement="top" ' +
                                                    'title="Este documento ha sido reenviado a autorización"></i>';
                    }

                    element.link = '<a href="' + this.oData.routeRmView + '/' + 
                                    element.idMaterialRequest + '">' + element.mrProvEntity + ' - '
                                    + element.mrFolio + '</a>';
                    if(element.mrStatus === 'EN AUTORIZACIÓN'){
                        if (this.oData.sessionUserName === element.userInTurn) {
                            element.authText = 'PENDIENTE PARA MÍ';
                        }
                        else {
                            element.authText = 'PENDIENTE PARA OTROS';
                        }
                    }
                }

                drawTableJson(
                    'table_rm',
                    this.vDocuments,
                    'idMaterialRequest',
                    'icons',
                    'link',
                    'mrType',
                    'mrDate',
                    'mrUser',
                    'authText',
                    'mrPriority',
                    'userInTurn',
                    'mrTotal',
                    'mrNature',
                    'mrItemReference',
                    'mrRequiredDate'
                );
            })
            .catch(error => {
                SGui.showError('Error al obtener los documentos' + error + '. Contacta a soporte técnico.');
                console.error('Error al obtener los documentos:', error);
                return [];
            });
        },
        onSelectRm(aData) {
            // redireccionar a la vista de documentos
            window.location.href = this.oData.routeRmView + '/' + aData[0];
        }
    },
    mounted() {
        // Obtener documentos al cargar la aplicación
        this.getDocuments(this.startDate, this.endDate);
    }
});