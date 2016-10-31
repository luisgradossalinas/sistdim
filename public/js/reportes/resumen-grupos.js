$(document).ready(function () {

    $("#generarExcel").hide();

    $('#tablaGrupos').dataTable({
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "lengthMenu": [[-1], ["All"]]
    });

    //Personalizar el listado de órganos
    $("#grupo_chzn").css('width', '310px');
    $("#grupo_chzn .chzn-drop").css('width', '300px');
    $("#grupo_chzn .chzn-drop .chzn-search input").css('width', '250px');

    generarExcel = function () {

        var grupo = $("#grupo").val();
        var nomgrupo = $("#grupo option:selected").text();

        if (grupo == '') {
            alert("Debe seleccionar un grupo");
            return false;
        }

        //Invocar ajax para generar el word
        $.ajax({
            url: urls.siteUrl + '/admin/reportes/export-excel-puesto-grupo',
            data: {
                grupo: grupo,
                nomgrupo: nomgrupo
            },
            type: 'post',
            dataType: 'json',
            success: function (result) {
                if (result.success == 1) {
                    location.href = "/Resumen-Puestos-Grupos.xlsx";
                }
            }
        });
    };

    $("#grupo").change(function () {

        var grupo = $("#grupo").val();
        $("#generarExcel").hide();
        if (grupo == '') {
            $('#tablaGrupos').DataTable().clear().draw();
            return false;
        }

        $('#tablaGrupos').DataTable().clear().draw();
        var nomgrupo = $("#grupo option:selected").text();

        if (grupo == '') {
            alert('Seleccione grupo');
            $('#tablaGrupos').DataTable().clear().draw();
            return false;
        }

        //Buscar y pintar la tablaGrupos de los puestos obtenidos
        $.ajax({
            url: urls.siteUrl + '/admin/reportes/obtener-puestos-grupos',
            data: {
                grupo: grupo
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
                    alert('No se encontraron registros.');
                    $('#tablaGrupos').DataTable().clear().draw();
                    return false;
                }

                $.each(result, function (key, obj) {
                    contador++;

                    var tdota = parseFloat(obj['dotacion']).toFixed(2).split(".");
                    if (parseInt(tdota[1]) >= urls.redondeo) {
                        tdota = parseInt(tdota[0]) + 1;
                    } else {
                        tdota = parseInt(tdota[0]);
                    }
                    tdotacion += tdota;

                    $('#tablaGrupos').DataTable().row.add([
                        '<center>' + contador + "</center>",
                        obj['grupo'],
                        obj['familia'],
                        obj['rol'],
                        obj['nombre_puesto'],
                        "<center>" + tdota + "</center>"
                    ]).draw(false);
                    $("#generarExcel").show();
                });

                //Agregando el total
                contador++;
                $('#tablaGrupos').DataTable().row.add([
                    '<center><div style="display:none">' + contador + "</div></center>",
                    '',
                    '',
                    "<center><b>" + "" + "</b></center>",
                    '<b>Total '+nomgrupo+'</b>',
                    "<center><b>" + tdotacion + "</b></center>"
                ]).draw(false);
                
            }
        });

    });
});
