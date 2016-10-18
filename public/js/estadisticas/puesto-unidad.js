$(document).ready(function () {
    
    var chart = new Highcharts.Chart(
            {"credits": {"enabled": false},
                "chart": {"renderTo": "jqchart"},
                "series": [{"name": "Browser share", "data": [["Firefox", 45], ["IE", 26.8], {"name": "Chrome", "y": 12.8, "sliced": true, "selected": true}, ["Safari", 8.5], ["Opera", 6.2], ["Others", 0.7]], "type": "column"}],
                "title": {"text": "Puestos por Unidad Orgánica"},
                "tooltip": {"formatter": function () {
                        return '<b>' + this.point.name + '</b>: ' + this.y + ' %';
                    }}, "plotOptions": {"pie": {"allowPointSelect": true, "cursor": "pointer",
                        "dataLabels": {"enabled": true, "color": "#000000", "connectorColor": "#000000", "formatter": function () {
                                return '<b>' + this.point.name + '</b>: ' + this.y + ' %'
                            }}}}});


    //Personalizar el listado de órganos
    $("#organo_chzn").css('width', '420px');
    $("#organo_chzn .chzn-drop").css('width', '410px');
    $("#organo_chzn .chzn-drop .chzn-search input").css('width', '360px');

    $("#unidad_chzn").css('width', '300px');
    $("#unidad_chzn .chzn-drop").css('width', '290px');
    $("#unidad_chzn .chzn-drop .chzn-search input").css('width', '240px');

    $("#organo").change(function () {

        var organo = $("#organo").val();
        if (organo == '') {
            $("#capa").html("<select id='unidad' style='width:320px'><option>[Selecione unidad orgánica]</option></select>");
            return false;
        }

        $.ajax({
            url: urls.siteUrl + '/admin/organigrama/obtener-uorganica',
            data: {
                organo: organo
            },
            type: 'post',
            //dataType: 'json',
            //Probar generando el html
            success: function (result) {
                $("#capa").html(result);
                $("#unidad").chosen();

                if ($("#unidad option").size() == 1) {
                    alert('Órgano, no tiene Unidades Orgánicas registradas.');
                    return false;
                }

                $("#unidad").change(function () {
                    var organo = $("#organo").val();
                    var unidad = $("#unidad").val();
                    var nomunidad = $("#unidad option:selected").text();

                    if (organo == '') {
                        alert('Seleccione órgano');
                        return false;
                    }
                    if (unidad == '') {
                        return false;
                    }
                    //Buscar y pintar la tablaOrgaUnidad de los puestos obtenidos
                    $.ajax({
                        url: urls.siteUrl + '/admin/estadisticas/puestos-dotacion', //Cambiar url solo totales
                        data: {
                            unidad: unidad
                        },
                        type: 'post',
                        dataType: 'json',
                        success: function (result) {

                            //Llenar tabla con los puestos
                            var contador = 0;
                            var totalQueda = 0;

                            var tcant = 0;
                            var tdotacion = 0;
                            var tqueda = 0;

                            var tdota = 0;

                            if (result == '' || result == []) {
                                alert('Unidad orgánica, no tiene puestos registrados.');
                                return false;
                            }
                            
                            //Actualizar gráfico
                            alert("Actualizar gráfico");

                            //0.56 es una persona
                            $.each(result, function (key, obj) {
                                contador++;

                                tdota = parseFloat(obj['total_dotacion']).toFixed(2).split(".");
                                if (parseInt(tdota[1]) >= urls.redondeo) {
                                    tdota = parseInt(tdota[0]) + 1;
                                } else {
                                    tdota = parseInt(tdota[0]);
                                }

                                tdotacion += tdota;
                                tcant += parseInt(obj['cantidad']);

                                totalQueda = tdota - parseInt(obj['cantidad']);

                            });

                            //Agregando el total
                            contador++;
                        }
                    });
                });
            }
        });
    });




});