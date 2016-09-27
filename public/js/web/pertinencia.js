var codigo = 0;
var sentencia_crud = '';
$(document).ready(function () {

    //Ocultar el botón listar puestos y nuevo puesto;
    $("#grabarPertinencia").hide();

    $('#tablaPertinencia').dataTable({
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

    grabarPertinencia = function () {

        if ($('#tablaPertinencia').DataTable().data().count() == 0) {
            alert('No existen puestos para grabar');
            return false;
        }

        var dataPertinencia = new Array();
        var contador = 0;
        var mensaje = '';
        var mostrarMensaje = 0;

        $("#tablaPertinencia tbody tr").each(function () {
            contador++;
            //Se muestra cuando si tiene mapa de puesto, agregar condicional
            var id_act = $(this).find("td input").eq(0).val();
            var id_tarea = $(this).find("td input").eq(1).val();
            var id_puesto = $(this).find("td input").eq(2).val();
            var nivel_puesto = $(this).find("td select").eq(0).val();
            var categoria_puesto = $(this).find("td select").eq(1).val();
            var nombre_puesto = $(this).find("td input").eq(5).val();

            if (nombre_puesto == '' || (nivel_puesto == '' || nivel_puesto == 0) || (categoria_puesto == '' || categoria_puesto == 0)) {
                mensaje += "En la fila " + contador + ": Debe completar todos los campos \n";
                mostrarMensaje = 1;
            }

            dataPertinencia.push(id_act + "|" + id_tarea + '|' + id_puesto + '|' + nivel_puesto
                    + "|" + categoria_puesto + "|" + nombre_puesto);

        });
        
        //Mostrar mensaje si existen datos por completar
        if (mostrarMensaje == 1) {
            alert(mensaje);
            return false;
        }

        $("#grabarPertinencia").attr('onclick', '');
        $.ajax({
            url: urls.siteUrl + '/admin/pertinencia/grabar-pertinencia',
            data: {
                pertinencia: dataPertinencia
            },
            type: 'post',
            dataType: 'json',
            success: function (result) {
                alert(result);
                $("#grabarPertinencia").attr('onclick', 'grabarPertinencia()');
            }
        });
    };

    $("#organo").change(function () {

        var organo = $("#organo").val();
        if (organo == '') {
            $('#tablaPertinencia').DataTable().clear().draw();
            $("#capa").html("<select id='unidad' style='width:320px'><option>[Selecione unidad orgánica]</option></select>");
            $("#capa_puesto").empty().append('<select id="puesto" name="puesto" style="width:320px"><option value="">[Seleccione Puesto]</option></select>');
            $("#puesto").chosen();
            return false;
        }

        $('#tablaPertinencia').DataTable().clear().draw();
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
                $("#capa_puesto").empty().append('<select id="puesto" name="puesto" style="width:320px"><option value="">[Seleccione Puesto]</option></select>');
                $("#puesto").chosen();

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
                        $('#tablaPertinencia').DataTable().clear().draw();
                        return false;
                    }
                    if (unidad == '') {
                        $('#tablaPertinencia').DataTable().clear().draw();
                        $("#grabarPertinencia").hide();
                        return false;
                    }
                    //Buscar y pintar la tablaPertinencia de los puestos obtenidos
                    $.ajax({
                        url: urls.siteUrl + '/admin/organigrama/obtener-puestos',
                        data: {
                            unidad: unidad
                        },
                        type: 'post',
                        dataType: 'json',
                        success: function (result) {

                            var html = '';
                            var contador = 0;
                            if (result == '' || result == []) {
                                alert('Unidad orgánica, no tiene puestos registrados.');
                                $('#tablaPertinencia').DataTable().clear().draw();
                                $("#grabarPertinencia").hide();
                                return false;
                            }

                            //Lenar listado de puestos
                            $('#tablaPertinencia').DataTable().clear().draw();
                            $("#capa_puesto").empty().append('<select id="puesto" name="puesto" style="width:320px"><option value="">[Seleccione Puesto]</option>');
                            $.each(result, function (key, obj) {
                                contador++;
                                $("#puesto").append("<option value='" + obj['id_puesto'] + "'>" + obj['puesto'] + "</option>");
                            });
                            $("#capa_puesto").append("</select>");
                            $("#puesto").chosen();
                            //$("#grabarPertinencia").show();

                            $("#puesto").change(function () {

                                var puesto = $("#puesto").val();
                                var nombre_puesto = $("#puesto option:selected").text();
                                $.ajax({
                                    url: urls.siteUrl + '/admin/pertinencia/obtener-actividad-puesto',
                                    data: {
                                        puesto: puesto
                                    },
                                    type: 'post',
                                    dataType: 'json',
                                    success: function (result) {

                                        var html = '';
                                        var contador = 0;

                                        if (result == '' || result == []) {
                                            alert('No existen actividades o tareas asignadas a: <b>' + nombre_puesto + "</b>");
                                            $('#tablaPertinencia').DataTable().clear().draw();
                                            $("#grabarPertinencia").hide();
                                            return false;
                                        }


                                        $('#tablaPertinencia').DataTable().clear().draw();
                                        $.each(result, function (key, obj) {
                                            contador++;
                                            $('#tablaPertinencia').DataTable().row.add([
                                                contador,
                                                "<input type=hidden name=id_actividad value='" + obj['id_actividad'] + "'>" + "<input type=hidden name=id_tarea value='" + obj['id_tarea'] + "'>"+ "<input type=hidden name=id_puesto value='" + obj['id_puesto'] + "'>" + obj['descripcion'],
                                                obj['tarea'],
                                                nombre_puesto,
                                                obj['nivel_puesto'],
                                                obj['categoria_puesto'],
                                                '<input type=text value="' + obj['nombre_puesto'] + '" style="font-size: 8pt">',
                                                ''
                                            ]).draw(false);
                                            $("#npuesto_" + contador).chosen();
                                            $("#npuesto_" + contador + "_chzn").css('font-size', '7pt');
                                            $("#cat_" + contador).chosen();
                                            $("#cat_" + contador + "_chzn").css('font-size', '6pt');
                                        });
                                        $("#grabarPertinencia").show();
                                    }
                                });
                            });
                        }
                    });
                });
            }
        });
    });
});
