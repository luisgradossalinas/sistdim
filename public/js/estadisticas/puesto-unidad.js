$(document).ready(function () {

    //$("#jqRadialGauge").hide();

    /*
     var chart = new Highcharts.Chart(
     {"credits": {"enabled": false},
     "chart": {"renderTo": "jqRadialGauge"},
     "series": [{"name": "Browser share", "data": [["Firefox", 45], ["IE", 26.8], {"name": "Chrome", "y": 12.8, "sliced": true, "selected": true}, ["Safari", 8.5], ["Opera", 6.2], ["Others", 0.7]], "type": "column"}],
     "title": {"text": "Puestos por Unidad Orgánica"},
     "tooltip": {"formatter": function () {
     return '<b>' + this.point.name + '</b>: ' + this.y + ' %';
     }}, "plotOptions": {"pie": {"allowPointSelect": true, "cursor": "pointer",
     "dataLabels": {"enabled": true, "color": "#000000", "connectorColor": "#000000", "formatter": function () {
     return '<b>' + this.point.name + '</b>: ' + this.y + ' %'
     }}}}});
     */

    var generarGrafico = function (data, unidad) {
        var chart = new Highcharts.Chart({
        //    $('#capa_grafico').jqChart({
            "chart": {"renderTo": "capa_grafico"},
            title: {text: 'Unidad Orgánica:' + unidad},
            legend: { title: 'Puestos' },
            animation: {duration: 1},
            shadows: {
                enabled: true
            },
            series: [
                {
                    type: 'column', //pie
                    title: 'Puestos',
                    //fillStyle: '#418CF0',
                    fillStyles: ['#418CF0', '#FCB441', '#E0400A', '#056492', '#BFBFBF', '#1A3B69', '#FFE382'],
                    data: data
                }
            ]
        });
    };


    //Personalizar el listado de órganos
    $("#organo_chzn").css('width', '420px');
    $("#organo_chzn .chzn-drop").css('width', '410px');
    $("#organo_chzn .chzn-drop .chzn-search input").css('width', '360px');

    $("#unidad_chzn").css('width', '300px');
    $("#unidad_chzn .chzn-drop").css('width', '290px');
    $("#unidad_chzn .chzn-drop .chzn-search input").css('width', '240px');

    $("#organo").change(function () {

        $("#capa_grafico").empty();
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
                        $("#capa_grafico").empty();
                        return false;
                    }

                    $.ajax({
                        url: urls.siteUrl + '/admin/estadisticas/puestos-dotacion',
                        dataType: 'json',
                        data: {unidad: unidad},
                        type: 'post',
                        success: function (result) {
                            generarGrafico(result, nomunidad);
                        }
                    });
                });
            }
        });
    });




});