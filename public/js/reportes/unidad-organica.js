$(document).ready(function () {

    $("#generarExcel").hide();
    $('#tablaUnidad').dataTable({
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "lengthMenu": [[-1], ["All"]]
    });
    generarExcel = function () {

        var organo = $("#organo").val();
        var unidad = $("#unidad").val();
        var nomorgano = $("#organo option:selected").text();
        var nomunidad = $("#unidad option:selected").text();
        if (organo == '' || unidad == '') {
            alert("Debe seleccionar Órgano o Unidad Orgánica");
            return false;
        }

        //Invocar ajax para generar el word
        $.ajax({
            url: urls.siteUrl + '/admin/reportes/export-excel-puesto-unidad',
            data: {
                unidad: unidad,
                nomorgano: nomorgano,
                nomunidad: nomunidad
            },
            type: 'post',
            dataType: 'json',
            success: function (result) {
                //Si se llegó a generar el word

                if (result.success == 1) {
                    location.href = "/Puestos-Unidad-Organica.xlsx";
                }

            }
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

        var organo = $("#organo").val();
        $("#generarExcel").hide();
        if (organo == '') {
            $('#tablaUnidad').DataTable().clear().draw();
            $("#capa").html("<select id='unidad' style='width:320px'><option>[Selecione unidad orgánica]</option></select>");
            return false;
        }

        $('#tablaUnidad').DataTable().clear().draw();
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
                    $('#tablaUnidad').DataTable().clear().draw();
                    if (organo == '') {
                        alert('Seleccione órgano');
                        $('#tablaUnidad').DataTable().clear().draw();
                        return false;
                    }
                    if (unidad == '') {
                        $('#tablaUnidad').DataTable().clear().draw();
                        $("#generarExcel").hide();
                        return false;
                    }
                    //Buscar y pintar la tablaUnidad de los puestos obtenidos
                    $.ajax({
                        url: urls.siteUrl + '/admin/reportes/obtener-puestos-grupos-unidad',
                        data: {
                            unidad: unidad
                        },
                        type: 'post',
                        dataType: 'json',
                        success: function (result) {

                            var contador = 0;
                            if (result == '' || result == []) {
                                alert('No se encontraron registros.');
                                $('#tablaUnidad').DataTable().clear().draw();
                                return false;
                            }

                            //0.56 es una persona
                            var tdotacion = 0;
                            $.each(result, function (key, obj) {
                                contador++;

                                var tdota = parseFloat(obj['dotacion']).toFixed(2).split(".");
                                if (parseInt(tdota[1]) >= urls.redondeo) {
                                    tdota = parseInt(tdota[0]) + 1;
                                } else {
                                    tdota = parseInt(tdota[0]);
                                }
                                tdotacion += tdota;

                                $('#tablaUnidad').DataTable().row.add([
                                    '<center>' + contador + "</center>",
                                    obj['organo'],
                                    obj['unidad'],
                                    obj['grupo'],
                                    obj['familia'],
                                    obj['rol'],
                                    obj['nombre_puesto'],
                                    "<center>" + tdota + "</center>"
                                ]).draw(false);
                                $("#generarExcel").show();
                            });

                            $('#tablaUnidad').DataTable().row.add([
                                '<span style="display:none">' + contador + "</span></center>",
                                '',
                                '',
                                '',
                                '',
                                '',
                                '<b>Total General</b>',
                                "<center><b>" + tdotacion + "</b></center>"
                            ]).draw(false);

                        }
                    });
                });
            }
        });
    });
});
