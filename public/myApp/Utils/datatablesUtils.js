function drawTable(table_name, lData = null){
    if(lData != null){
        table[table_name].clear().draw();
        table[table_name].rows.add(lData).draw();
    }else{
        table[table_name].draw();
    }
}

function renderInTable(table_name, column, elements){
    table[table_name].rows().every(function(rowIdx) {
        var row = this;
        var checkBoxTd = $(row.node()).find('td:eq('+column+')'); // accede a la celda, el index de la celda es apartir de la primer celda que se muestra en la vista, las olcultas no cuentan

        checkBoxTd.html(elements[rowIdx]);
      });
}

function drawTableJson(nameTable, jsonData, ...keys){
    if(jsonData != null){
        try {
            var arrayData = [];
            
            jsonData.forEach(function(item) {
                var newItem = [];
                
                keys.forEach(function(key) {
                    newItem.push(item[key]);
                });
                
                arrayData.push(newItem);
            });
    
            table[nameTable].clear().draw();
            table[nameTable].rows.add(arrayData).draw();
        } catch (error) {
            // location.reload();
            console.log(error);
        }
    }else{
        table[nameTable].draw();
    }
}