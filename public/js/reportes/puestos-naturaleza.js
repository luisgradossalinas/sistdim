$(document).ready(function () {

    $("#generarExcel").hide();
    $('#tablaAnaPertinencia').dataTable({
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
            url: urls.siteUrl + '/admin/reportes/export-excel-puestos-naturaleza',
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
                    location.href = "/Puestos-Naturaleza.xlsx";
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
            $('#tablaAnaPertinencia').DataTable().clear().draw();
            $("#capa").html("<select id='unidad' style='width:320px'><option>[Selecione unidad orgánica]</option></select>");
            return false;
        }

        $('#tablaAnaPertinencia').DataTable().clear().draw();
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
                    $('#tablaAnaPertinencia').DataTable().clear().draw();
                    if (organo == '') {
                        alert('Seleccione órgano');
                        $('#tablaAnaPertinencia').DataTable().clear().draw();
                        return false;
                    }
                    if (unidad == '') {
                        $('#tablaAnaPertinencia').DataTable().clear().draw();
                        $("#generarExcel").hide();
                        return false;
                    }
                    //Buscar y pintar la tablaAnaPertinencia de los puestos obtenidos
                    $.ajax({
                        url: urls.siteUrl + '/admin/organigrama/obtener-puestos',
                        data: {
                            unidad: unidad
                        },
                        type: 'post',
                        dataType: 'json',
                        success: function (result) {

                            var contador = 0;
                            if (result == '' || result == []) {
                                alert('Falta grabar la pertinencia en los puestos');
                                $('#tablaAnaPertinencia').DataTable().clear().draw();
                                return false;
                            }

                            //0.56 es una persona
                            var tcantidad = 0;
                            $.each(result, function (key, obj) {
                                contador++;

                                tcantidad += parseInt(obj['cantidad']);

                                $('#tablaAnaPertinencia').DataTable().row.add([
                                    '<center>' + contador + "</center>",
                                    obj['naturaleza'],
                                    obj['puesto'],
                                    "<center>" + obj['cantidad'] + "</center>"
                                ]).draw(false);
                                $("#generarExcel").show();
                            });

                            $('#tablaAnaPertinencia').DataTable().row.add([
                                '<span style="display:none">' + contador + "</span></center>",
                                '',
                                'Total General',
                                "<center>" + tcantidad + "</center>"
                            ]).draw(false);
                        }
                    });
                });
            }
        });
    });
});
