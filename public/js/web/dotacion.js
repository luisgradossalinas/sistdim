$(document).ready(function () {


    $("#capa_nombre").hide();

    //Ocultar el botón listar puestos y nuevo puesto;
    $("#grabarDotacion").hide();
    $("#tablaResumen").hide();

    $('#tablaDotacion').dataTable({
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

    calcularDotacion = function () {

        if ($('#tablaDotacion').DataTable().data().count() == 0) {
            alert('No existen registros');
            return false;
        }

        var contador = 0;
        var dotacion = 0;
        var resultP = '';
        var periodicidad = '';
        var resultT = '';
        var tiempo = '';

        $("#tablaDotacion tbody tr").each(function () {
            contador++;

            if ($(this).find("td select").eq(0).val() != '') {
                resultP = $(this).find("td select").eq(0).val().split('|');
                periodicidad = resultP[1];
            } else {
                periodicidad = '';
            }

            var frecuencia = $(this).find("td input").eq(3).val();

            if ($(this).find("td select").eq(1).val() != '') {
                resultT = $(this).find("td select").eq(1).val().split('|');
                tiempo = resultT[1];
            } else {
                tiempo = '';
            }
            var duracion = $(this).find("td input").eq(5).val();

            //No considerarlo en la suma
            if (frecuencia == '' || (periodicidad == '' || periodicidad == 0) || (tiempo == '' || tiempo == 0)
                    || duracion == '') {

            } else {
                //Realizar suma
                dotacion += ((periodicidad * frecuencia * tiempo * duracion) * 1.1) / 176;
            }
        });

        $("#dotacionPuesto").empty().append(dotacion.toFixed(2));

    };

    //Sumar al cambiar select de la tabla
    $('#tablaDotacion').on('change', 'tr td select', function () {
        calcularDotacion();
    });

    //Sumar al cambiar valor en frecuencia y tiempo
    $('#tablaDotacion').on('keyup', 'tr td input', function () {
        calcularDotacion();
    });

    grabarDotacion = function () {

        var nombre_trabajador = '';

        if ($('#tablaDotacion').DataTable().data().count() == 0) {
            alert('No existen registros para grabar');
            return false;
        }

        if ($("#rol").val() == 3) { //Invitado
            if ($("#nombre_trabajador").val() == '') {
                alert("Ingrese nombre");
                return false;
                
            }
            nombre_trabajador = $("#nombre_trabajador").val();
        }

        var dataDotacion = new Array();
        var contador = 0;
        var mensaje = '';
        var mostrarMensaje = 0;

        $("#tablaDotacion tbody tr").each(function () {
            contador++;
            //Se muestra cuando si tiene mapa de puesto, agregar condicional
            var id_act = $(this).find("td input").eq(0).val();
            var id_tarea = $(this).find("td input").eq(1).val();
            var resultP = $(this).find("td select").eq(0).val().split('|');
            var periodicidad = resultP[0];
            var frecuencia = $(this).find("td input").eq(3).val();
            var resultT = $(this).find("td select").eq(1).val().split('|');
            var tiempo = resultT[0];
            var duracion = $(this).find("td input").eq(5).val();

            if (frecuencia == '' || (periodicidad == '' || periodicidad == 0) || (tiempo == '' || tiempo == 0)
                    || duracion == '') {
                mensaje += "En la fila " + contador + ": Debe completar todos los campos \n";
                mostrarMensaje = 1;
            }

            dataDotacion.push(id_act + "|" + id_tarea + '|' + periodicidad + '|' + frecuencia
                    + "|" + tiempo + "|" + duracion);

        });


        //Mostrar mensaje si existen datos por completar
        if (mostrarMensaje == 1) {
            alert(mensaje);
            return false;
        }

        $("#grabarDotacion").attr('onclick', '');
        $.ajax({
            url: urls.siteUrl + '/admin/dotacion/grabar-dotacion',
            data: {
                dotacion: dataDotacion,
                totalDotacion: $("#dotacionPuesto").text(),
                puesto: $("#puesto").val(),
                nombre_trabajador: nombre_trabajador
            },
            type: 'post',
            dataType: 'json',
            success: function (result) {
                alert(result);
                $("#grabarDotacion").attr('onclick', 'grabarDotacion()');
            }
        });
    };

    $("#organo").change(function () {

        $("#tablaResumen").hide();
        var organo = $("#organo").val();
        if (organo == '') {
            $('#tablaDotacion').DataTable().clear().draw();
            $("#capa").html("<select id='unidad' style='width:320px'><option>[Selecione unidad orgánica]</option></select>");
            $("#capa_puesto").empty().append('<select id="puesto" name="puesto" style="width:320px"><option value="">[Seleccione Puesto]</option></select>');
            $("#puesto").chosen();
            return false;
        }

        $('#tablaDotacion').DataTable().clear().draw();
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

                    $("#tablaResumen").hide();
                    var organo = $("#organo").val();
                    var unidad = $("#unidad").val();
                    var nomunidad = $("#unidad option:selected").text();

                    if (organo == '') {
                        alert('Seleccione órgano');
                        $('#tablaDotacion').DataTable().clear().draw();
                        return false;
                    }
                    if (unidad == '') {
                        $('#tablaDotacion').DataTable().clear().draw();
                        $("#grabarDotacion").hide();
                        $("#capa_puesto").empty().append('<select id="puesto" name="puesto" style="width:320px"><option value="">[Seleccione Puesto]</option>');
                        $("#capa_puesto").append("</select>");
                        $("#puesto").chosen();


                        return false;
                    }
                    //Buscar y pintar la tablaDotacion de los puestos obtenidos
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
                                $('#tablaDotacion').DataTable().clear().draw();
                                $("#grabarDotacion").hide();
                                return false;
                            }

                            //Lenar listado de puestos
                            $('#tablaDotacion').DataTable().clear().draw();
                            $("#capa_puesto").empty().append('<select id="puesto" name="puesto" style="width:320px"><option value="">[Seleccione Puesto]</option>');
                            $.each(result, function (key, obj) {
                                contador++;
                                $("#puesto").append("<option value='" + obj['id_puesto'] + "'>" + obj['puesto'] + "</option>");
                            });
                            $("#capa_puesto").append("</select>");
                            $("#puesto").chosen();

                            $("#puesto").change(function () {

                                var puesto = $("#puesto").val();
                                var nombre_puesto = $("#puesto option:selected").text();
                                $.ajax({
                                    url: urls.siteUrl + '/admin/dotacion/obtener-dotacion',
                                    data: {
                                        puesto: puesto
                                    },
                                    type: 'post',
                                    dataType: 'json',
                                    success: function (result) {

                                        var contador = 0;
                                        var totalDotacion = 0;

                                        if (result == '' || result == []) {
                                            alert('No existen actividades o tareas asignadas a: <b>' + nombre_puesto + "</b>");
                                            $('#tablaDotacion').DataTable().clear().draw();
                                            $("#grabarDotacion").hide();
                                            $("#tablaResumen").hide();
                                            return false;
                                        }

                                        $('#tablaDotacion').DataTable().clear().draw();
                                        $.each(result, function (key, obj) {
                                            contador++;
                                            totalDotacion = parseFloat(obj['total_dotacion']);
                                            $('#tablaDotacion').DataTable().row.add([
                                                '<center>' + contador + "</center>",
                                                "<input type=hidden name=id_actividad value='" + obj['id_actividad'] + "'>" + "<input type=hidden name=id_tarea value='" + obj['id_tarea'] + "'>" + obj['nivel0'],
                                                obj['nivel1'],
                                                obj['nivel2'],
                                                obj['nivel3'],
                                                obj['nivel4'],
                                                obj['descripcion'],
                                                obj['tarea'],
                                                obj['periodicidad'],
                                                '<center><input type=text id=frecuencia_' + contador + ' value="' + obj['frecuencia'] + '" style="font-size: 8pt;width:70%;text-align:center" maxlength="6"></center>',
                                                obj['tiempo'],
                                                '<center><input type=text id=duracion_' + contador + ' value="' + obj['duracion'] + '" style="font-size: 8pt;width:70%;text-align:center" maxlength="6"></center>',
                                                ''
                                            ]).draw(false);
                                            $("#frecuencia_" + contador).numeric();
                                            $("#duracion_" + contador).numeric();

                                            $("#periodicidad_" + contador).chosen();
                                            $("#periodicidad_" + contador + "_chzn").css('font-size', '7.5pt');
                                            $("#periodicidad_" + contador + "_chzn").css('width', '94%');

                                            $("#tiempo_" + contador).chosen();
                                            $("#tiempo_" + contador + "_chzn").css('font-size', '7.5pt');
                                            $("#tiempo_" + contador + "_chzn").css('width', '94%');

                                        });
                                        $("#grabarDotacion").show();
                                        $("#tablaResumen").show();
                                        if ($("#rol").val() == 3) { //Invitado
                                            $("#capa_nombre").show();
                                        }
                                        $("#textoPuesto").empty().append(nombre_puesto);
                                        $("#dotacionPuesto").empty().append(totalDotacion.toFixed(2));

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
