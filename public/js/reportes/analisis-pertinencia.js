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
                        url: urls.siteUrl + '/admin/organigrama/obtener-puestos',
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
                                $('#tablaAnaPertinencia').DataTable().clear().draw();
                                return false;
                            }

                            //0.56 es una persona
                            $.each(result, function (key, obj) {
                                contador++;
                                
                                tdota = parseFloat(obj['total_dotacion']).toFixed(2).split(".");
                                if (parseInt(tdota[1]) >= 56) {
                                    tdota = parseInt(tdota[0]) + 1;
                                } else {
                                    tdota = parseInt(tdota[0]);
                                }
                                
                                tdotacion += tdota;
                                tcant += parseInt(obj['cantidad']); 
                                
                                totalQueda = tdota - parseInt(obj['cantidad']);
                                
                                $('#tablaAnaPertinencia').DataTable().row.add([
                                    '<center>' + contador + "</center>",
                                    obj['puesto'],
                                    "<center>" + obj['cantidad'] + "</center>",
                                    "<center>" + tdota + "</center>",
                                    "<center>" + totalQueda + "</center>"
                                ]).draw(false);

                            });

                            //Agregando el total
                            contador++;
                            tqueda = tdotacion - tcant;
                            $('#tablaAnaPertinencia').DataTable().row.add([
                                '<center><div style="display:none">' + contador + "</div></center>",
                                '<b>Total</b>',
                                "<center><b>" + tcant + "</b></center>",
                                "<center><b>" + tdotacion + "</b></center>",
                                "<center><b>" + tqueda + "</b></center>"
                            ]).draw(false);
                        }
                    });
                });
            }
        });
    });
});
