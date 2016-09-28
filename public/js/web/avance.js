$(document).ready(function () {

    $('#tablaAvance').dataTable({
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "lengthMenu": [[-1], ["All"]]
    });

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
            $('#tablaAvance').DataTable().clear().draw();
            $("#capa").html("<select id='unidad' style='width:320px'><option>[Selecione unidad orgánica]</option></select>");
            return false;
        }

        $('#tablaAvance').DataTable().clear().draw();
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
                    $('#tablaAvance').DataTable().clear().draw();

                    if (organo == '') {
                        alert('Seleccione órgano');
                        $('#tablaAvance').DataTable().clear().draw();
                        return false;
                    }
                    if (unidad == '') {
                        $('#tablaAvance').DataTable().clear().draw();
                        return false;
                    }
                    //Buscar y pintar la tablaAvance de los puestos obtenidos
                    $.ajax({
                        url: urls.siteUrl + '/admin/organigrama/obtener-puestos',
                        data: {
                            unidad: unidad
                        },
                        type: 'post',
                        dataType: 'json',
                        success: function (result) {

                            //Llenar tabla con los puestos
                            var contador = 0;
                            var totalDotacion = 0;
                            if (result == '' || result == []) {
                                alert('Unidad orgánica, no tiene puestos registrados.');
                                $('#tablaAvance').DataTable().clear().draw();
                                return false;
                            }


                            $.each(result, function (key, obj) {
                                contador++;
                                totalDotacion += parseFloat(obj['total_dotacion']);
                                $('#tablaAvance').DataTable().row.add([
                                    '<center>' + contador + "</center>",
                                    obj['puesto'],
                                    "<center>" + parseFloat(obj['total_dotacion']).toFixed(2) + "</center>",
                                    "<center>" + obj['nombre_trabajador'] + "</center>",
                                    ''
                                ]).draw(false);

                            });

                            //Agregando el total
                            contador++;
                            $('#tablaAvance').DataTable().row.add([
                                '<center><div style="display:none">' + contador + "</div></center>",
                                '<b>Total</b>',
                                "<center><b>" + totalDotacion + "</b></center>",
                                '',
                                ''
                            ]).draw(false);

                        }
                    });
                });
            }
        });
    });
});
