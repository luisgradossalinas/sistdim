$(document).ready(function () {

    $('#tablaAnaPertinencia').dataTable({
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
                        return false;
                    }
                    //Buscar y pintar la tablaAnaPertinencia de los puestos obtenidos
                    $.ajax({
                        url: urls.siteUrl + '/admin/pertinencia/obtener-puestos',
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
                            $.each(result, function (key, obj) {
                                contador++;
                                
                                if (obj['dotacion'] == null) {
                                    obj['dotacion'] = "0.00";
                                }
                                

                                $('#tablaAnaPertinencia').DataTable().row.add([
                                    '<center>' + contador + "</center>",
                                    obj['puesto'],
                                    obj['descripcion'],
                                    obj['nombre_puesto'],
                                    "<center>" + obj['dotacion'] + "</center>"
                                ]).draw(false);

                            });
                        }
                    });
                });
            }
        });
    });
});
