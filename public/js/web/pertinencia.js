var codigo = 0;
var sentencia_crud = '';
$(document).ready(function () {

    //Ocultar el botón listar puestos y nuevo puesto;
    $("#grabarPuestos").hide();

    $('#tablaPertinencia').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
                 "lengthMenu": [[-1], [ "All"]]
               // "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>"
		//"sDom": '<""l>t<"F"fp>'
	});

    //Personalizar el listado de órganos
    $("#organo_chzn").css('width', '420px');
    $("#organo_chzn .chzn-drop").css('width', '410px');
    $("#organo_chzn .chzn-drop .chzn-search input").css('width', '360px');

    $("#unidad_chzn").css('width', '300px');
    $("#unidad_chzn .chzn-drop").css('width', '290px');
    $("#unidad_chzn .chzn-drop .chzn-search input").css('width', '240px');


    grabarPuestos = function () {

        alert("revisar");
        return false;

        var mapaPuesto = $("#mapaPuesto").val();
        var control = 0;
        if ($('#tablaPertinencia').DataTable().data().count() == 0) {
            alert('No existen puestos para grabar');
            return false;
        }

        if (mapaPuesto == 1) {
            control = 1;
        }

        var dataPuesto = new Array();
        var nombrePuesto = new Array();
        var codPuesto = new Array();
        var contador = 0;
        var mensaje = '';
        var mostrarMensaje = 0;
        var nombre_iguales = '';
        var nomRepetidos = '';
        var cod_iguales = '';
        var codRepetidos = '';

        $("#tablaPertinencia tbody tr").each(function () {
            contador++;
            var id_puesto = $(this).find("td input").val();
            //Se muestra cuando si tiene mapa de puesto, agregar condicional
            var correlativo = $(this).find("td input").eq(1 - control).val();
            var nom_puesto = $(this).find("td input").eq(2 - control).val();
            var cantidad = $(this).find("td input").eq(3 - control).val();
            var grupo = $(this).find("td select").eq(0).val();
            var familia = $(this).find("td select").eq(1).val();
            var rol = $(this).find("td select").eq(2).val();
            var unidad = $(this).find("td input").eq(4 - control).val();

            if (mapaPuesto == 0) {
                if ((correlativo == '' || correlativo == 0) || nom_puesto == '' ||
                        (cantidad == '' || cantidad == 0) || grupo == '' || familia == '' || rol == '') {
                    mensaje += "En la fila " + contador + ": Debe completar todos los campos \n";
                    mostrarMensaje = 1;
                }
            } else if (mapaPuesto == 1) {
                if (nom_puesto == '' || (cantidad == '' || cantidad == 0) || grupo == '' || familia == '' || rol == '') {
                    mensaje += "En la fila " + contador + ": Debe completar todos los campos \n";
                    mostrarMensaje = 1;
                }
                correlativo = '';
            }
            dataPuesto.push(id_puesto + "|" + correlativo + '|' + nom_puesto + '|' + cantidad
                    + "|" + grupo + "|" + familia + "|" + rol + "|" + unidad);


            $.each(nombrePuesto, function (index, value) {
                if (value == nom_puesto) {
                    nombre_iguales = "Existen puestos con el mismo nombre:";
                    nomRepetidos += "<br>-" + nom_puesto + "";
                }
            });
            nombrePuesto.push(nom_puesto);
            if (mapaPuesto == 0) {
                $.each(codPuesto, function (index, value) {
                    if (value == correlativo) {
                        cod_iguales = "Existen códigos repetidos:";
                        codRepetidos += "<br>-" + correlativo + "";
                    }
                });
            }

            codPuesto.push(correlativo);

        });

        //Mostrar mensaje si existen datos por completar
        if (mostrarMensaje == 1) {
            alert(mensaje);
            return false;
        }

        //Mostrar mensaje de nombres de puestos duplicados
        if (nombre_iguales != '') {
            alert(nombre_iguales + nomRepetidos);
            return false;
        }

        //Mostrar mensaje de códigos repetidos
        if (cod_iguales != '') {
            alert(cod_iguales + codRepetidos);
            return false;
        }


        $("#grabarPuestos").attr('onclick', '');
        $.ajax({
            url: urls.siteUrl + '/admin/organigrama/grabar-puestos',
            data: {
                puestos: dataPuesto
            },
            type: 'post',
            dataType: 'json',
            success: function (result) {
                alert(result);
                $("#grabarPuestos").attr('onclick', 'grabarPuestos()');
                //No refrescar página, sino actualizar con ajac los id
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
                            alert('No existen puestos, ingrese Nuevos puestos');
                            $('#tablaPertinencia').DataTable().clear().draw();
                            return false;
                        }

                        $('#tablaPertinencia').DataTable().clear().draw();
                        $.each(result, function (key, obj) {
                            contador++;
                            $('#tablaPertinencia').DataTable().row.add([
                                contador,
                                "<input type=hidden name=id_puesto value='" + obj['id_puesto'] + "'>" + obj['organo'],
                                obj['unidad'],
                                "<input type=number name=num_cor value='" + obj['numcor'] + "' style='width:50%'>",
                                "<input type=textarea name=puesto class='puesto_validate' value='" + obj['puesto'] + "'>",
                                "<input type=number name=cantidad value='" + obj['cantidad'] + "' style='width:50%'>",
                                obj['grupo'],
                                obj['familia'],
                                obj['rpuesto'] + "<input type=hidden name=unidadT value='" + unidad + "'>"
                            ]).draw(false);
                        });
                    }
                });
                //location.reload();
            }
        });
    };

    //Actualizar las tablaPertinencias de los órganos y las unidades orgánicas
    grabarDatos = function (tipo) {

        var data = new Array();
        var validar = '';
        $("#tablaPertinencia" + tipo + " tbody tr").each(function () {
            var id = $(this).attr("data-" + tipo);
            var descripcion = $(this).find("td input").eq(1).val();
            var idp = $(this).find("td select").eq(0).val();
            //Siglas obtener
            var siglas = $(this).find("td input").eq(2).val();
            validar = $(this).find("td").eq(0).text();
            data.push(id + "|" + descripcion + "|" + idp + "|" + siglas);
        });

        //if (validar == 'No hay registros' || validar == 'No hay datos en la tablaPertinencia') {
        if ($('#tablaPertinencia' + tipo).DataTable().data().count() == 0) {
            alert("No hay registros que actualizar");
            return false;
        }

        $.ajax({
            url: urls.siteUrl + '/admin/organigrama/grabar/tipo/' + tipo,
            data: {
                datos: data
            },
            type: 'post',
            dataType: 'json',
            success: function (result) {
                alert(result);
                location.reload();
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
        //Realizar ajax para buscar unidades orgánicas
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
                        $("#grabarPuestos").hide();
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
                                $("#grabarPuestos").hide();
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
                            //$("#grabarPuestos").show();

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
                                            alert('No existen actividades ... tareas');
                                            $('#tablaPertinencia').DataTable().clear().draw();
                                            $("#grabarPuestos").hide();
                                            return false;
                                        }

                                        
                                        $('#tablaPertinencia').DataTable().clear().draw();
                                        $.each(result, function (key, obj) {
                                            contador++;
                                            $('#tablaPertinencia').DataTable().row.add([
                                                contador,
                                                obj['descripcion'],
                                                '',
                                                nombre_puesto,
                                                obj['nivel_puesto'],
                                                obj['categoria_puesto'],
                                               '<input type=text value="'+obj['nombre_puesto']+'" style="font-size: 8pt">',
                                               ''
                                            ]).draw(false);
                                            $("#npuesto_"+contador).chosen();
                                            $("#npuesto_" + contador + "_chzn").css('font-size', '7pt');
                                            $("#cat_"+contador).chosen();
                                            $("#cat_" + contador + "_chzn").css('font-size', '7pt');
                                        });
                                        $("#grabarPuestos").show();
                                        
                                    }
                                });
                            });
                        }
                    });
                });
            }
        });
    });


})
