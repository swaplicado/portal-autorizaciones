// import axios from 'axios'; // Importación correcta de Axios

var app = new Vue({
    el: '#appDps',
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
            await axios.get(this.oData.routeDps, {
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
                    element.link = '<a href="' + this.oData.routeDpsView + '/' + 
                                    element.idYear + '/' + 
                                    element.idDoc + '">' + 
                                    element.dpsFolio + '</a>';
                }

                drawTableJson(
                    'table_dps',
                    this.vDocuments,
                    'idYear',
                    'idDoc',
                    'dt',
                    'link',
                    'provider',
                    'authText',
                    'userInTurn',
                    'costCenters',
                    'subTotalCur',
                    'totalCur',
                    'currency',
                    'exchangeRate',
                    'matReqFolio',
                    'matReqDt',
                    'matReqUser',
                    'dpsUser',
                );
            })
            .catch(error => {
                SGui.showError('Error al obtener los documentos' + error + '. Contacta a soporte técnico.');
                console.error('Error al obtener los documentos:', error);
                return [];
            });
        },
        onSelectDps(aData) {
            // redireccionar a la vista de documentos
            window.location.href = this.oData.routeDpsView + '/' + aData[0] + '/' + aData[1];
        }
    },
    mounted() {
        // Obtener documentos al cargar la aplicación
        this.getDocuments(this.startDate, this.endDate);
    }
});
