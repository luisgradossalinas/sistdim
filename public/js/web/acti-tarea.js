var sentencia_crud = '';
$(document).ready(function () {

    //Ocultar tabla
    $("#tabla_wrapper").hide();
    //Grabar proceso
    $("#grabarActividad").click(function () {

        var nivel = $("#nivel").val();
        var dataActividad = new Array();
        var contador = 0;
        var mensaje = '';
        var mostrarMensaje = 0;

        //Variables para guardar
        var unidad;
        var puesto;
        var proceso; //Código del proceso donde se van a agregar las actividades
        var id_actividad;
        var actividad;
        var tieneTarea = '';

        if (nivel == 0) {
            alert("Para agregar actividades debe elegir un proceso mayor al nivel 1");
            return false;
        }

        if ($('#tabla').DataTable().data().count() == 0) {
            alert('No existen actividades para grabar');
            return false;
        }
        
        //Recorrer toda la tabla
        $("#tabla tbody tr").each(function () {
            contador++;

            id_actividad = $(this).find("td input").eq(0).val();
            proceso = $(this).find("td input").eq(1).val();
            actividad = $(this).find("td input").eq(2).val();
            
            unidad = $(this).find("td select").eq(1).val();
            puesto = $(this).find("td select").eq(2).val();
            tieneTarea = $(this).find("td select").eq(0).val();

            //Validar que cuando se tenga tarea no se obligue seleccionar unidad y puest
            if (tieneTarea == 1) {
                if (actividad == '') {
                    mensaje += "En la fila " + contador + ": Debe completar todos los campos \n";
                    mostrarMensaje = 1;
                }
                unidad = '';
                puesto = '';
            } else if (tieneTarea == 0) {
                if (actividad == '' || (unidad == '' || unidad == 0) ||
                        (puesto == '' || puesto == 0)) {
                    mensaje += "En la fila " + contador + ": Debe completar todos los campos \n";
                    mostrarMensaje = 1;
                }
            }
            dataActividad.push(id_actividad + "|" + proceso + "|" + actividad + "|" + unidad + "|" + puesto + "|" + tieneTarea);
        });
        
        //Mostrar mensaje si existen datos por completar
        if (mostrarMensaje == 1) {
            alert(mensaje);
            return false;
        }
        
        //Validar que se agreguen de 2 a más actividades
        if ($('#tabla').DataTable().data().count() / 5 == 1) {
            alert('Debe agregar más de 1 actividad para poder grabar.');
            return false;
        }

/*
        console.log(dataActividad);
        alert("probando no grabar");
        return false;
*/

        $.ajax({
            url: urls.siteUrl + '/admin/procesos/grabar-actividad',
            data: {
                actividad: dataActividad,
                proceso: proceso,
                n: nivel
            },
            type: 'post',
            dataType: 'json',
            success: function (result) {
                alert(result);
                $.ajax({
                    url: urls.siteUrl + '/admin/procesos/obtener-actividad',
                    data: {
                        proceso: proceso,
                        nivel: nivel
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function (result) {
                        var contador = 0;
                        //Primero validar que se obtenga data
                        if (result == '' || result == []) {
                            $('#tabla').DataTable().clear().draw();
                            $("#tabla_wrapper").show();
                            $("#nuevaActividad").show();
                            $("#grabarActividad").show();
                            return false;
                        }

                        $('#tabla').DataTable().clear().draw();
                        $.each(result, function (key, obj) {
                            contador++;

                            //Evaluar si tiene tarea
                            var select = '<div style="float:left"><select id=tarea_' + contador + '>';
                            if (obj['tiene_tarea'] == 1) { //Si tiene tarea
                                select += '<option value=1 selected>Sí</option>';
                                select += '<option value=0>No</option>';

                            } else {
                                select += '<option value=1>Sí</option>';
                                select += '<option value=0 selected>No</option>';
                            }

                            select += '</select></div>';
                            var detalleTarea = '<div id="capa_tarea_' + contador + '" style="float:left">\n\
                        <a id="nuevaTarea_' + contador + '" id_actividad=' + obj['id_actividad'] + ' nom_act="' + obj['descripcion'] + '" role="button" class="btn btn-default btn-xs tip-right" title="Ver/Añadir tareas" \n\
                        ><li class="icon-list"></li></a></div>';


                            $('#tabla').DataTable().row.add([
                                contador + " " + '<a id="editPosicion_' + contador + '"' + " posicion=" + obj['codigo_actividad'] + " id_proceso=" + obj['id_proceso'] + " id_actividad='" + obj['id_actividad'] + "' nom_act='" + obj['descripcion'] + "'" + ' role="button" class="btn btn-default btn-xs tip-right" title="Cambiar posición"><li class="icon-list"></li></a>',
                                "<input type=hidden id='id_actividad' value='" + obj['id_actividad'] + "'><input type=hidden id='id_proceso' value='" + proceso + "'><input type=text name=n1_" + contador + " id=n1_" + contador + " value='" + obj['descripcion'] + "' style='width:95%;font-size:8pt'>",
                                select + detalleTarea,
                                obj['unidad'] + '<span style="display:none">' + obj['descripcion'] + "</span>",
                                obj['puesto']
                            ]).draw(false);

                            $("#tarea_" + contador).chosen();
                            $("#tarea_" + contador + "_chzn").css('font-size', '7.5pt');
                            $("#tarea_" + contador + "_chzn").css('width', '50px');
                            $("#tarea_" + contador + "_chzn .chzn-drop .chzn-search input").css('width', '50px');

                            if (obj['tiene_tarea'] == 0) {
                                $("#capa_tarea_" + contador).hide();
                            }

                            $("#unidad_" + contador).chosen();
                            $("#unidad_" + contador + "_chzn").css('font-size', '7.5pt');
                            $("#unidad_" + contador + "_chzn").css('width', '230px');
                            $("#puesto_" + contador).chosen();
                            $("#puesto_" + contador + "_chzn").css('font-size', '7.5pt');
                            $("#puesto_" + contador + "_chzn").css('width', '230px');

                            if (obj['tiene_tarea'] == 1) { //Si tiene tarea
                                $("#unidad_" + contador + "_chzn").hide();
                                $("#puesto_" + contador + "_chzn").hide();
                            }

                        });

                        $("#nuevaActividad").show();
                        $("#grabarActividad").show();
                        $("#tabla_wrapper").show();
                    }
                });
            }
        });

        console.log(dataActividad);


    });

    var ocultarSelect = function () {
        $("#n0_chzn").hide();
        $("#n1_chzn").hide();
        $("#n2_chzn").hide();
        $("#n3_chzn").hide();
        $("#n4_chzn").hide();
        $("#nuevaActividad").hide();
        $("#grabarActividad").hide();
    };

    ocultarSelect();

    $('#tabla').on('change', 'tr td select', function () {

        var id = ($(this).attr("id"));
        var valor = $(this).val();
        var result = id.split('_');
        var tipo = result[0];
        var num = result[1];

        if (tipo == 'unidad') {
            $.ajax({
                url: urls.siteUrl + '/admin/procesos/obtener-puestos-actividades',
                data: {
                    unidad: valor,
                    num: num,
                    tarea: ''
                },
                type: 'post',
                //dataType: 'json',
                success: function (result) {
                    $("#capa_" + num).empty().append(result);
                    //$("#puesto_" + num).empty().append(result);
                    $("#puesto_" + num).chosen();
                    $("#puesto_" + num + "_chzn").css('font-size', '7.5pt');
                    $("#puesto_" + num + "_chzn").css('width', '230px');
                }
            });
        } else if (tipo == 'tarea') {

            if (valor == 1) { //Tiene tarea ocultar
                $("#unidad_" + num + "_chzn").hide();
                $("#puesto_" + num + "_chzn").hide();
                $("#capa_tarea_" + num).show();
            } else {
                alert('Si la actividad tiene tareas, se eliminarán.');
                $("#unidad_" + num + "_chzn").show();
                $("#puesto_" + num + "_chzn").show();
                $("#capa_tarea_" + num).hide();
            }

        }
    });

    //Nueva tarea
    $('#tabla').on('click', 'tr td a', function () {
        var id = ($(this).attr("id"));
        var valor = $(this).val();

        if (id == '' || id == null) {
            return false;
        }

        var result = id.split('_');
        var tipo = result[0];
        var num = result[1];
        var id_actividad = ($(this).attr("id_actividad"));
        var nom_act = ($(this).attr("nom_act"));
        var posicion = ($(this).attr("posicion"));
        var proceso = ($(this).attr("id_proceso"));

        var nivel = $("#nivel").val();
        var nom_n0 = $("#n0 option:selected").text();
        var nom_n1 = $("#n1 option:selected").text();
        var nom_n2 = $("#n2 option:selected").text();
        var nom_n3 = $("#n3 option:selected").text();
        var nom_n4 = $("#n4 option:selected").text();

        //Invocar ajax obteniendo las tareas de la actividad
        //Abrir popup con las tareas de las actividades
        if (tipo == 'nuevaTarea') {

            $.ajax({
                url: urls.siteUrl + '/admin/procesos/obtener-tarea',
                data: {
                    actividad: id_actividad
                },
                type: 'post',
                dataType: 'json',
                success: function (result) {
                    tablaTareas(result, nivel, id_actividad, nom_act, nom_n0, nom_n1, nom_n2, nom_n3, nom_n4);
                }
            });
        } else if (tipo == 'editPosicion') {

            $('#ventana-modal').empty().html('<b>Actividad:</b> ' + nom_act + '<br>' + "<b>Posición:</b> <input type=number id='posicion' value= " + posicion + " style='width:8%'>");
            $("#ventana-modal").dialog({
                modal: true,
                width: 540,
                height: 180,
                title: 'Cambiar posición de actividad',
                resizable: false,
                buttons: {
                    'Cambiar posición': function () {

                        var nueva_posicion = $("#posicion").val();

                        //Si no se cambió no hacer nada, no ha cambiado su posición
                        if (posicion == nueva_posicion) {
                            alert("No se ha cambiado posición");
                            return false;
                        }

                        if (nueva_posicion <= 0) {
                            alert("Debe ingresar un número mayor a 0");
                            return false;
                        }

                        //Guardar nueva posición
                        $.ajax({
                            url: urls.siteUrl + '/admin/procesos/cambiar-posicion',
                            data: {
                                nivel: nivel, proceso: proceso, tipo: 'actividad',
                                actividad: id_actividad, anterior: posicion, nueva: nueva_posicion
                            },
                            type: 'post',
                            dataType: 'json',
                            success: function (result) {
                                alert(result);
                                $('#ventana-modal').dialog("close");
                                //Actualizar las posiciones con ajax
                                $.ajax({
                                    url: urls.siteUrl + '/admin/procesos/obtener-actividad',
                                    data: {
                                        proceso: proceso,
                                        nivel: nivel
                                    },
                                    type: 'post',
                                    dataType: 'json',
                                    success: function (result) {
                                        var contador = 0;
                                        //Primero validar que se obtenga data
                                        if (result == '' || result == []) {
                                            $('#tabla').DataTable().clear().draw();
                                            $("#tabla_wrapper").show();
                                            $("#nuevaActividad").show();
                                            $("#grabarActividad").show();
                                            return false;
                                        }

                                        $('#tabla').DataTable().clear().draw();
                                        $.each(result, function (key, obj) {
                                            contador++;

                                            //Evaluar si tiene tarea
                                            var select = '<div style="float:left"><select id=tarea_' + contador + '>';
                                            if (obj['tiene_tarea'] == 1) { //Si tiene tarea
                                                select += '<option value=1 selected>Sí</option>';
                                                select += '<option value=0>No</option>';

                                            } else {
                                                select += '<option value=1>Sí</option>';
                                                select += '<option value=0 selected>No</option>';
                                            }

                                            select += '</select></div>';
                                            var detalleTarea = '<div id="capa_tarea_' + contador + '" style="float:left">\n\
                        <a id="nuevaTarea_' + contador + '" id_actividad=' + obj['id_actividad'] + ' nom_act="' + obj['descripcion'] + '" role="button" class="btn btn-default btn-xs tip-right" title="Ver/Añadir tareas" \n\
                        ><li class="icon-list"></li></a></div>';


                                            $('#tabla').DataTable().row.add([
                                                contador + " " + '<a id="editPosicion_' + contador + '"' + " posicion=" + obj['codigo_actividad'] + " id_proceso=" + obj['id_proceso'] + " id_actividad='" + obj['id_actividad'] + "' nom_act='" + obj['descripcion'] + "'" + ' role="button" class="btn btn-default btn-xs tip-right" title="Cambiar posición"><li class="icon-list"></li></a>',
                                                "<input type=hidden id='id_actividad' value='" + obj['id_actividad'] + "'><input type=hidden id='id_proceso' value='" + proceso + "'><input type=text name=n1_" + contador + " id=n1_" + contador + " value='" + obj['descripcion'] + "' style='width:95%;font-size:8pt'>",
                                                select + detalleTarea,
                                                obj['unidad'] + '<span style="display:none">' + obj['descripcion'] + "</span>",
                                                obj['puesto']
                                            ]).draw(false);

                                            $("#tarea_" + contador).chosen();
                                            $("#tarea_" + contador + "_chzn").css('font-size', '7.5pt');
                                            $("#tarea_" + contador + "_chzn").css('width', '50px');
                                            $("#tarea_" + contador + "_chzn .chzn-drop .chzn-search input").css('width', '50px');

                                            if (obj['tiene_tarea'] == 0) {
                                                $("#capa_tarea_" + contador).hide();
                                            }

                                            $("#unidad_" + contador).chosen();
                                            $("#unidad_" + contador + "_chzn").css('font-size', '7.5pt');
                                            $("#unidad_" + contador + "_chzn").css('width', '230px');
                                            $("#puesto_" + contador).chosen();
                                            $("#puesto_" + contador + "_chzn").css('font-size', '7.5pt');
                                            $("#puesto_" + contador + "_chzn").css('width', '230px');

                                            if (obj['tiene_tarea'] == 1) { //Si tiene tarea
                                                $("#unidad_" + contador + "_chzn").hide();
                                                $("#puesto_" + contador + "_chzn").hide();
                                            }

                                        });

                                        $("#nuevaActividad").show();
                                        $("#grabarActividad").show();
                                        $("#tabla_wrapper").show();
                                    }
                                });
                            }
                        });
                    },
                    Cerrar: function () {
                        $(this).dialog("close");
                    }
                }
            });

        }



    });

    tablaTareas = function (data, nivel, id_act, nom_act, n0, n1, n2, n3, n4) {

        $('.modal-body').empty();
        var html = '';
        html += '<div style="font-size:8pt">';
        html += '<b>Nivel 0:</b> ' + n0;

        if (nivel == 1) {
            html += ' <br><b>Nivel 1:</b> ' + n1;
        } else if (nivel == 2) {
            html += '<br><b>Nivel 1:</b> ' + n1;
            html += '<br><b>Nivel 2:</b> ' + n2;
        } else if (nivel == 3) {
            html += '<br><b>Nivel 1:</b> ' + n1;
            html += '<br><b>Nivel 2:</b> ' + n2;
            html += '<br><b>Nivel 3:</b> ' + n3;
        } else if (nivel == 4) {
            html += '<br><b>Nivel 1:</b> ' + n1;
            html += '<br><b>Nivel 2:</b> ' + n2;
            html += '<br><b>Nivel 3:</b> ' + n3;
            html += '<br><b>Nivel 4:</b> ' + n4;
        }

        html += '<input type=hidden id=idact value=' + id_act + '>';
        html += '<br><b>Actividad:</b> ' + nom_act;
        //Agregar botón para agregar tareas
        html += ' <a id="nuevaTarea" role="button" class="btn tip-left" title="Nueva tarea" \n\
        onclick="nuevaTarea()">Nueva tarea<li class="icon-plus-sign"></li></a>';
        html += '</div>';

        html += '<div class="widget-box">';
        html += '<div class="widget-title">';
        html += '<h5>Tareas</h5>';
        html += '</div>';
        html += '<div class="widget-content nopadding">';
        html += '<table id="tablaTarea" width="100%" class="table table-condensed table-bordered" style="font-size: 7pt">';
        html += '<thead>';
        html += '<tr><th width="7%">#</th><th width="55%">Tarea</th><th width="19%">Unidad Orgánica</th><th width="19%">Puesto</th></tr>';
        html += '</thead>';
        html += '<tbody>';
        html += '</tbody>';
        html += '</table>';
        html += '</div>';
        html += '</div>';

        $('#ventana-modal').empty().html(html);       
        $('#tablaTarea').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
                 "lengthMenu": [[50, -1], [50, "All"]]
	});

        var contador = 0;
        $.each(data, function (key, obj) {
            contador++;
            $('#tablaTarea').DataTable().row.add([
                contador + " " + '<a id="editPosicionT_' + contador + '"' + " posicion=" + obj['codigo_tarea'] + " id_tarea=" + obj['id_tarea'] + " id_actividad='" + obj['id_actividad'] + "' nom_tarea='" + obj['descripcion'] + "'" + ' role="button" class="btn btn-default btn-xs tip-right" title="Cambiar posición"><li class="icon-list"></li></a>',
                "<input type=hidden id='id_tarea' value='" + obj['id_tarea'] + "'><input type=hidden id='id_actividad' value='" + id_act + "'><input type=text name=n1_" + contador + " value='" + obj['descripcion'] + "'  id=n1_" + contador + " style='width:95%;font-size:7.5pt'>",
                obj['unidad'] + '<span style="display:none">' + obj['descripcion'] + "</span>",
                obj['puesto']
            ]).draw(false);
            $("#tunidad_" + contador).chosen();
            $("#tunidad_" + contador + "_chzn").css('font-size', '7.5pt');
            $("#tunidad_" + contador + "_chzn").css('width', '230px');
            $("#tpuesto_" + contador).chosen();
            $("#tpuesto_" + contador + "_chzn").css('font-size', '7.5pt');
            $("#tpuesto_" + contador + "_chzn").css('width', '230px');
        });

        $('#tablaTarea').on('click', 'tr td a', function () {
            var id = ($(this).attr("id"));
            var valor = $(this).val();

            if (id == '' || id == null) {
                return false;
            }

            var result = id.split('_');
            var tipo = result[0];
            var num = result[1];
            var id_tarea = ($(this).attr("id_tarea"));
            var nom_tarea = ($(this).attr("nom_tarea"));
            var posicion = ($(this).attr("posicion"));
            var id_actividad = ($(this).attr("id_actividad"));

            //Invocar ajax obteniendo las tareas de la actividad
            //Abrir popup con las tareas de las actividades
            if (tipo == 'editPosicionT') {

                $('#ventana-modal2').empty().html('<b>Tarea:</b> ' + nom_tarea + '<br>' + "<b>Posición:</b> <input type=number id='posicion' value= " + posicion + " style='width:8%'>");
                $("#ventana-modal2").dialog({
                    modal: true,
                    width: 540,
                    height: 180,
                    title: 'Cambiar posición de tarea',
                    resizable: false,
                    buttons: {
                        'Cambiar posición': function () {

                            var nueva_posicion = $("#posicion").val();

                            //Si no se cambió no hacer nada, no ha cambiado su posición
                            if (posicion == nueva_posicion) {
                                alert("No se ha cambiado posición");
                                return false;
                            }

                            if (nueva_posicion <= 0) {
                                alert("Debe ingresar un número mayor a 0");
                                return false;
                            }

                            //Guardar nueva posición
                            $.ajax({
                                url: urls.siteUrl + '/admin/procesos/cambiar-posicion',
                                data: {
                                    actividad: id_actividad, tipo: 'tarea',
                                    tarea: id_tarea, anterior: posicion, nueva: nueva_posicion
                                },
                                type: 'post',
                                dataType: 'json',
                                success: function (result) {
                                    alert(result);
                                    $('#ventana-modal2').dialog("close");
                                    $('#ventana-modal').dialog("close");
                                    //Actualizar las posiciones con ajax
                                    //ajax obtener actividades
                                }
                            });

                        },
                        Cerrar: function () {
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });

        $('#tablaTarea').on('change', 'tr td select', function () {

            var id = ($(this).attr("id"));
            var valor = $(this).val();
            var result = id.split('_');
            var tipo = result[0];
            var num = result[1];

            if (tipo == 'tunidad') {
                $.ajax({
                    url: urls.siteUrl + '/admin/procesos/obtener-puestos-actividades',
                    data: {
                        unidad: valor,
                        num: num,
                        tarea: 't'
                    },
                    type: 'post',
                    success: function (result) {
                        $("#tcapa_" + num).empty().append(result);
                        $("#tpuesto_" + num).chosen();
                        $("#tpuesto_" + num + "_chzn").css('font-size', '7.5pt');
                        $("#tpuesto_" + num + "_chzn").css('width', '230px');
                    }
                });
            }
        });

        $('#ventana-modal').dialog({
            height: 500,
            width: 1000,
            modal: true,
            resizable: false,
            title: 'Lista de tareas',
            buttons: {
                "Grabar tareas": function () {

                    if ($('#tablaTarea').DataTable().data().count() == 0) {
                        alert('No existen tareas para grabar');
                        return false;
                    }

                    var dataTarea = new Array();
                    var contador = 0;
                    var mensajito = '';
                    var mostrarMensaje = 0;

                    //Variables para guardar
                    var unidad;
                    var puesto;
                    var id_tarea;
                    var id_actividad;
                    var tarea;

                    dialog = $(this);
                    //Primero validar que todo los campos estén completos
                    //Recorrer toda la tabla
                    $("#tablaTarea tbody tr").each(function () {
                        contador++;

                        id_tarea = $(this).find("td input").eq(0).val();
                        id_actividad = $(this).find("td input").eq(1).val();
                        tarea = $(this).find("td input").eq(2).val();
                        unidad = $(this).find("td select").eq(0).val();
                        puesto = $(this).find("td select").eq(1).val();
                        dataTarea.push(id_tarea + "|" + id_actividad + "|" + tarea + "|" + unidad + "|" + puesto);
                        if (tarea == '' || (unidad == '' || unidad == 0) ||
                                (puesto == '' || puesto == 0)) {
                            mensajito += "En la fila " + contador + ": Debe completar todos los campos \n";
                            mostrarMensaje = 1;
                        }
                    });

                    //Mostrar mensaje si existen datos por completar
                    if (mostrarMensaje == 1) {
                        alert(mensajito);
                        return false;
                    }
                    
                    if ($('#tablaTarea').DataTable().data().count() / 4 == 1) {
                        alert('Debe agregar más de 1 tarea para poder grabar.');
                        return false;
                    }


                    //Ajax para grabr y actualizar tareas
                    $.ajax({
                        url: urls.siteUrl + '/admin/procesos/grabar-tarea',
                        data: {
                            tarea: dataTarea,
                            actividad: id_act
                        },
                        type: 'post',
                        dataType: 'json',
                        success: function (result) {
                            alert(result);
                            $('#ventana-modal').dialog("close");
                        }
                    });

                },
                "Cancelar": function () {
                    $(this).dialog("close");
                }
            },
            close: function () {
            }
        });
    };

    nuevaTarea = function () {

        var numReg = ($('#tablaTarea').DataTable().data().count() / 4) + 1;
        var id_act = $("#idact").val();

        $("#nuevaTarea").attr('onclick', '');
        $.ajax({
            url: urls.siteUrl + '/admin/procesos/obtener-uorganica',
            data: {num: numReg, tarea: 't'},
            type: 'post',
            success: function (result) {

                $("#nuevaTarea").attr('onclick', 'nuevaTarea()');
                $('#tablaTarea').DataTable().row.add([
                    numReg,
                    "<input type=hidden id='id_tarea' value='0'><input type=hidden id='id_actividad' value='" + id_act + "'><input type=text name=n1_" + numReg + " id=n1_" + numReg + " style='width:95%;font-size:8pt'>",
                    result,
                    "<div id='tcapa_" + numReg + "'></div>"
                ]).draw(false);

                $("#tunidad_" + numReg).chosen();
                $("#tunidad_" + numReg + "_chzn").css('font-size', '7.5pt');
                $("#tunidad_" + numReg + "_chzn").css('width', '230px');
                $("#tcapa_" + numReg).empty().append("<select id='tpuesto_" + numReg + "'><option value=''>[Seleccione puesto]</option></select>");
                $("#tpuesto_" + numReg).chosen();
                $("#tpuesto_" + numReg + "_chzn").css('font-size', '7.5pt');
                $("#tpuesto_" + numReg + "_chzn").css('width', '230px');

            }
        });

    };

    //Nueva actividad
    nuevaActividad = function () {

        var nivel = $("#nivel").val();
        var n0 = $("#n0").val();
        var n0_nom = $("#n0 option:selected").text();
        var n1 = $("#n1").val();
        var n2 = $("#n2").val();
        var n3 = $("#n3").val();
        var n4 = $("#n3").val();
        var proceso;

        if (nivel == '') {
            $("#tabla_wrapper").hide();
            $('#tabla').DataTable().clear().draw();
            return false;
        }
        if (nivel == 1 && n1 == '') {
            alert("Seleccione nivel");
            return false;
        }
        if (nivel == 2 && n2 == '') {
            alert("Seleccione nivel");
            return false;
        }
        if (nivel == 3 && n3 == '') {
            alert("Seleccione nivel");
            return false;
        }
        if (nivel == 4 && n4 == '') {
            alert("Seleccione nivel");
            return false;
        }

        var numReg = ($('#tabla').DataTable().data().count() / 5) + 1;

        $("#nuevaActividad").attr('onclick', '');
        if (nivel >= 1) {

            if (nivel == 1) {
                proceso = $("#n1").val();
            } else if (nivel == 2) {
                proceso = $("#n2").val();
            } else if (nivel == 3) {
                proceso = $("#n3").val();
            } else if (nivel == 4) {
                proceso = $("#n4").val();
            }

            $.ajax({
                url: urls.siteUrl + '/admin/procesos/obtener-uorganica',
                data: {num: numReg, tarea: ''},
                type: 'post',
                success: function (result) {

                    //aqui select
                    var select = '<div style="float:left"><select id=tarea_' + numReg + '>';
                    //Si tiene tarea
                    select += '<option value=1>Sí</option>';
                    select += '<option value=0 selected>No</option>';
                    select += '</select></div>';

                    $("#nuevaActividad").attr('onclick', 'nuevaActividad()');
                    $('#tabla').DataTable().row.add([
                        numReg,
                        "<input type=hidden id='id_actividad' value='0'><input type=hidden id='id_proceso' value='" + proceso + "'><input type=text name=n1_" + numReg + " id=n1_" + numReg + " style='width:95%;font-size:8pt'>",
                        select,
                        result,
                        "<div id='capa_" + numReg + "'></div>"
                    ]).draw(false);

                    $("#tarea_" + numReg).chosen();
                    $("#tarea_" + numReg + "_chzn").css('font-size', '7.5pt');
                    $("#tarea_" + numReg + "_chzn").css('width', '50px');
                    $("#tarea_" + numReg + "_chzn .chzn-drop .chzn-search input").css('width', '50px');

                    $("#unidad_" + numReg).chosen();
                    $("#unidad_" + numReg + "_chzn").css('font-size', '7.5pt');
                    $("#unidad_" + numReg + "_chzn").css('width', '230px');
                    $("#capa_" + numReg).empty().append("<select id='puesto_" + numReg + "'><option value=''>[Seleccione puesto]</option></select>");
                    $("#puesto_" + numReg).chosen();
                    $("#puesto_" + numReg + "_chzn").css('font-size', '7.5pt');
                    $("#puesto_" + numReg + "_chzn").css('width', '230px');
                    $("#tabla_wrapper").show();
                    $("#nuevaActividad").show();
                }
            });
        }
    };

    $("#nivel").change(function () {

        var nivel = $(this).val();
        setearListas();
        $('#tabla').DataTable().clear().draw();
        $("#tabla_wrapper").hide();
        $('#n0 option[value=""]').attr('selected', 'selected');
        $("#n0_chzn a span").empty().append('[Proceso nivel 0]');
        $("#n0_chzn_o_0").attr('class', 'active-result result-selected');

        if (nivel == '') {
            ocultarSelect();
            $("#tabla_wrapper").hide();
            $("#n0").hide();
            $("#n1").hide();
            $("#n2").hide();
            $("#n3").hide();
            $("#n4").hide();
            $("#tabla_wrapper").hide();
            $("#nuevaActividad").hide();
            $("#grabarActividad").hide();

        } else if (nivel == 1) {
            $("#n0_chzn").show();
            //$("#n1_chzn").hide();
            $("#n2_chzn").hide();
            $("#n3_chzn").hide();
            $("#n4_chzn").hide();

            //$("#n1").show();
            $("#n2").hide();
            $("#n3").hide();
            $("#n4").hide();

            $("#nuevaActividad").hide();
            $("#grabarActividad").hide();
            $("#tabla_wrapper").hide();
            $('#tabla').DataTable().clear().draw();
        } else if (nivel == 2) {
            $("#n0_chzn").show();

            $("#n1_chzn").hide();
            $("#n1").show();
            $("#n1").chosen();

            //$("#n2").show();
            $("#n3").hide();
            $("#n4").hide();

            //$("#n2_chzn").hide();
            $("#n3_chzn").hide();
            $("#n4_chzn").hide();
            $("#nuevaActividad").hide();
            $("#grabarActividad").hide();
            $("#tabla_wrapper").hide();
            $('#tabla').DataTable().clear().draw();
        } else if (nivel == 3) {
            $("#n0_chzn").show();

            $("#n1_chzn").hide();
            $("#n1").show();
            $("#n2_chzn").hide();
            $("#n2").show();

            $("#n3").show();
            $("#n4").hide();

            $("#n3_chzn").hide();
            $("#n4_chzn").hide();
            $("#nuevaActividad").hide();
            $("#grabarActividad").hide();
            $("#tabla_wrapper").hide();
            $('#tabla').DataTable().clear().draw();
        } else if (nivel == 4) {
            $("#n0_chzn").show();
            $("#n1_chzn").hide();
            $("#n2_chzn").hide();

            $("#n1").show();
            $("#n2").show();
            $("#n3_chzn").hide();
            $("#n3").show();

            $("#n4").show();

            $("#n4_chzn").hide();
            $("#nuevaActividad").hide();
            $("#grabarActividad").hide();
            $("#tabla_wrapper").hide();
            $('#tabla').DataTable().clear().draw();

        }
    });

    var setearListas = function () {

        $("#n1").empty().append("<option value=''>[Proceso nivel 1]</option>");
        $("#n1_chzn .chzn-results").empty().append('<li id="n1_chzn_o_0" class="active-result result-selected" style="">[Proceso nivel 1]</li>');
        $("#n1_chzn a span").empty().append('[Proceso nivel 1]');
        $("#n2").empty().append("<option value=''>[Proceso nivel 2]</option>");
        $("#n2_chzn .chzn-results").empty().append('<li id="n2_chzn_o_0" class="active-result" style="">[Proceso nivel 2]</li>');
        $("#n2_chzn a span").empty().append('[Proceso nivel 2]');
        $("#n3").empty().append("<option value=''>[Proceso nivel 3]</option>");
        $("#n3_chzn .chzn-results").empty().append('<li id="n3_chzn_o_0" class="active-result" style="">[Proceso nivel 3]</li>');
        $("#n3_chzn a span").empty().append('[Proceso nivel 3]');
        $("#n4").empty().append("<option value=''>[Proceso nivel 4]</option>");
        $("#n4_chzn .chzn-results").empty().append('<li id="n4_chzn_o_0" class="active-result" style="">[Proceso nivel 4]</li>');
        $("#n4_chzn a span").empty().append('[Proceso nivel 4]');
    };

    setearListas();

    $("#n0").change(function () {

        var n0 = $("#n0").val();
        var nivel = parseInt($("#nivel").val());
        $('#tabla').DataTable().clear().draw();
        $("#tabla_wrapper").hide();
        $("#nuevaActividad").hide();
        $("#grabarActividad").hide();
        //Si no se ha seleccionado proceso y es nivel 0, 
        if (n0 == '' || nivel == 0) {
            return false
        }

        if (nivel >= 1) {
            $.ajax({
                url: urls.siteUrl + '/admin/procesos/obtener-proceso-nivel1-actividad',
                data: {
                    n0: n0,
                    nivel: nivel
                },
                type: 'post',
                dataType: 'json',
                success: function (result) {
                    var contador = 0;
                    $("#n1").empty().append("<option value=''>[Proceso nivel 1]</option>");
                    $("#n1_chzn .chzn-results").empty().append('<li id="n1_chzn_o_0" class="active-result result-selected" style="">[Proceso nivel 1]</li>');
                    $("#n1_chzn a span").empty().append('[Proceso nivel 1]');
                    $("#n2").empty().append("<option value=''>[Proceso nivel 2]</option>");
                    $("#n2_chzn .chzn-results").empty().append('<li id="n2_chzn_o_0" class="active-result" style="">[Proceso nivel 2]</li>');
                    $("#n2_chzn a span").empty().append('[Proceso nivel 2]');
                    $("#n3").empty().append("<option value=''>[Proceso nivel 3]</option>");
                    $("#n3_chzn .chzn-results").empty().append('<li id="n3_chzn_o_0" class="active-result" style="">[Proceso nivel 3]</li>');
                    $("#n3_chzn a span").empty().append('[Proceso nivel 3]');
                    $("#n4").empty().append("<option value=''>[Proceso nivel 4]</option>");
                    $("#n4_chzn .chzn-results").empty().append('<li id="n4_chzn_o_0" class="active-result" style="">[Proceso nivel 4]</li>');
                    $("#n4_chzn a span").empty().append('[Proceso nivel 4]');
                    //Primero validar que se obtenga data
                    if (result == '' || result == []) {
                        $('#tabla').DataTable().clear().draw();
                        return false;
                    }

                    $('#tabla').DataTable().clear().draw();

                    $("#div_n1").empty().append('<select id="n1" name="n1"><option value="">[Proceso nivel 1]</option>');

                    if (nivel >= 1) {
                        $.each(result, function (key, obj) {
                            contador++;
                            $("#n1").append("<option value='" + obj['id_proceso_n1'] + "'>" + obj['descripcion'] + "</option>");
                            //$("#n1_chzn .chzn-drop .chzn-results").append('<li id="n1_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');
                        });
                    }
                    $("#div_n1").append("</select>");
                    $("#n1").chosen();

                    //inicio
                    $("#n1").change(function () {

                        var n0 = $("#n0").val();
                        var n1 = $("#n1").val();
                        var nivel = parseInt($("#nivel").val());
                        var select = '';
                        var detalleTarea = '';
                        $('#tabla').DataTable().clear().draw();
                        $("#tabla_wrapper").hide();
                        $("#nuevaActividad").hide();
                        $("#grabarActividad").hide();

                        //Si no se ha seleccionado proceso y es nivel 0, 
                        //no ejecutar ajax
                        if (n1 == '' || nivel == '') {
                            return false;
                        }

                        if (nivel > 1) {
                            $.ajax({
                                url: urls.siteUrl + '/admin/procesos/obtener-proceso-nivel2-actividad',
                                data: {
                                    n1: n1,
                                    nivel: nivel
                                },
                                type: 'post',
                                dataType: 'json',
                                success: function (result) {
                                    var contador = 0;
                                    $("#n2").empty().append("<option value=''>[Proceso nivel 2]</option>");
                                    $("#n2_chzn .chzn-results").empty().append('<li id="n2_chzn_o_0" class="active-result" style="">[Proceso nivel 2]</li>');
                                    $("#n2_chzn a span").empty().append('[Proceso nivel 2]');
                                    $("#n3").empty().append("<option value=''>[Proceso nivel 3]</option>");
                                    $("#n3_chzn .chzn-results").empty().append('<li id="n3_chzn_o_0" class="active-result" style="">[Proceso nivel 3]</li>');
                                    $("#n3_chzn a span").empty().append('[Proceso nivel 3]');

                                    //Primero validar que se obtenga data
                                    if (result == '' || result == []) {
                                        $("#tabla_wrapper").show();
                                        $("#nuevaActividad").show();
                                        $("#grabarActividad").show();
                                        //alert('Proceso no tiene nivel 2');
                                        //return false;
                                    }

                                    $('#tabla').DataTable().clear().draw();
                                    $("#div_n2").empty().append('<select id="n2" name="n2"><option value="">[Proceso nivel 2]</option>');
                                    if (nivel >= 2) {
                                        $.each(result, function (key, obj) {
                                            contador++;
                                            $("#n2").append("<option value='" + obj['id_proceso_n2'] + "'>" + obj['descripcion'] + "</option>");
                                            //$("#n2_chzn .chzn-results").append('<li id="n2_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');
                                        });
                                        $("#div_n2").append("</select>");
                                        $("#n2").chosen();
                                        $("#tabla_wrapper").hide();
                                        $("#nuevaActividad").hide();
                                        $("#grabarActividad").hide();

                                        $("#n2").change(function () {

                                            var n2 = $("#n2").val();
                                            var nivel = parseInt($("#nivel").val());
                                            $('#tabla').DataTable().clear().draw();
                                            $("#tabla_wrapper").hide();
                                            $("#nuevaActividad").hide();
                                            $("#grabarActividad").hide();
                                            //Si no se ha seleccionado proceso y es nivel 0, 
                                            //no ejecutar ajax
                                            if (n2 == '' || nivel == '') {
                                                return false
                                            }

                                            if (nivel > 2) {
                                                $.ajax({
                                                    url: urls.siteUrl + '/admin/procesos/obtener-proceso-nivel3-actividad',
                                                    data: {
                                                        n2: n2,
                                                        nivel: nivel
                                                    },
                                                    type: 'post',
                                                    dataType: 'json',
                                                    success: function (result) {

                                                        var contador = 0;
                                                        $("#n3").empty().append("<option value=''>[Proceso nivel 3]</option>");
                                                        $("#n3_chzn .chzn-results").empty().append('<li id="n3_chzn_o_0" class="active-result" style="">[Proceso nivel 3]</li>');
                                                        $("#n3_chzn a span").empty().append('[Proceso nivel 3]');
                                                        //Primero validar que se obtenga data
                                                        if (result == '' || result == []) {
                                                            return false;
                                                        }
                                                        $('#tabla').DataTable().clear().draw();

                                                        $("#div_n3").empty().append('<select id="n3" name="n3"><option value="">[Proceso nivel 3]</option>');
                                                        $.each(result, function (key, obj) {
                                                            contador++;
                                                            $("#n3").append("<option value='" + obj['id_proceso_n3'] + "'>" + obj['descripcion'] + "</option>");
                                                            //$("#n3_chzn .chzn-results").append('<li id="n3_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');
                                                        });
                                                        $("#div_n3").append("</select>");
                                                        $("#n3").chosen();

                                                        $("#n3").change(function () {

                                                            var n3 = $("#n3").val();
                                                            var nivel = parseInt($("#nivel").val());
                                                            $('#tabla').DataTable().clear().draw();
                                                            $("#tabla_wrapper").hide();
                                                            $("#nuevaActividad").hide();
                                                            $("#grabarActividad").hide();

                                                            //Si no se ha seleccionado proceso y es nivel 0, 
                                                            //no ejecutar ajax
                                                            if (n3 == '' || nivel == '') {
                                                                return false
                                                            }

                                                            if (nivel > 3) {
                                                                $.ajax({
                                                                    url: urls.siteUrl + '/admin/procesos/obtener-proceso-nivel4-actividad',
                                                                    data: {n3: n3,
                                                                        nivel: nivel
                                                                    },
                                                                    type: 'post',
                                                                    dataType: 'json',
                                                                    success: function (result) {

                                                                        var contador = 0;
                                                                        $("#n4").empty().append("<option value=''>[Proceso nivel 4]</option>");
                                                                        $("#n4_chzn .chzn-results").empty().append('<li id="n4_chzn_o_0" class="active-result" style="">[Proceso nivel 4]</li>');
                                                                        $("#n4_chzn a span").empty().append('[Proceso nivel 4]');
                                                                        //Primero validar que se obtenga data
                                                                        if (result == '' || result == []) {
                                                                            return false;
                                                                        }

                                                                        $('#tabla').DataTable().clear().draw();

                                                                        $("#div_n4").empty().append('<select id="n4" name="n4"><option value="">[Proceso nivel 4]</option>');
                                                                        $.each(result, function (key, obj) {
                                                                            contador++;
                                                                            $("#n4").append("<option value='" + obj['id_proceso_n4'] + "'>" + obj['descripcion'] + "</option>");
                                                                            //$("#n4_chzn .chzn-results").append('<li id="n4_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');
                                                                        });
                                                                        $("#div_n4").append("</select>");
                                                                        $("#n4").chosen();

                                                                        $("#n4").change(function () {

                                                                            var n4 = $("#n4").val();
                                                                            var nivel = parseInt($("#nivel").val());
                                                                            $('#tabla').DataTable().clear().draw();
                                                                            $("#tabla_wrapper").hide();
                                                                            $("#nuevaActividad").hide();
                                                                            $("#grabarActividad").hide();

                                                                            //Si no se ha seleccionado proceso y es nivel 0, 
                                                                            //no ejecutar ajax
                                                                            if (n4 == '' || nivel == '') {
                                                                                return false
                                                                            }

                                                                            $.ajax({
                                                                                url: urls.siteUrl + '/admin/procesos/obtener-actividad',
                                                                                data: {
                                                                                    proceso: n4,
                                                                                    nivel: nivel
                                                                                },
                                                                                type: 'post',
                                                                                dataType: 'json',
                                                                                success: function (result) {

                                                                                    var contador = 0;
                                                                                    //Primero validar que se obtenga data
                                                                                    if (result == '' || result == []) {
                                                                                        if (nivel == 4) {
                                                                                            $('#tabla').DataTable().clear().draw();
                                                                                            $("#tabla_wrapper").show();
                                                                                            $("#nuevaActividad").show();
                                                                                            $("#grabarActividad").show();
                                                                                        }
                                                                                        return false;
                                                                                    }

                                                                                    $('#tabla').DataTable().clear().draw();
                                                                                    $.each(result, function (key, obj) {
                                                                                        contador++;
                                                                                        //Evaluar si tiene tarea
                                                                                        var select = '<div style="float:left"><select id=tarea_' + contador + '>';
                                                                                        if (obj['tiene_tarea'] == 1) { //Si tiene tarea
                                                                                            select += '<option value=1 selected>Sí</option>';
                                                                                            select += '<option value=0>No</option>';

                                                                                        } else {
                                                                                            select += '<option value=1>Sí</option>';
                                                                                            select += '<option value=0 selected>No</option>';
                                                                                        }

                                                                                        select += '</select></div>';
                                                                                        var detalleTarea = '<div id="capa_tarea_' + contador + '" style="float:left">\n\
                        <a id="nuevaTarea_' + contador + '" id_actividad=' + obj['id_actividad'] + ' nom_act="' + obj['descripcion'] + '" role="button" class="btn btn-default btn-xs tip-right" title="Ver/Añadir tareas" \n\
                        ><li class="icon-list"></li></a></div>';

                                                                                        $('#tabla').DataTable().row.add([
                                                                                            contador + " " + '<a id="editPosicion_' + contador + '"' + " posicion=" + obj['codigo_actividad'] + " id_proceso=" + n4 + " id_actividad='" + obj['id_actividad'] + "' nom_act='" + obj['descripcion'] + "'" + ' role="button" class="btn btn-default btn-xs tip-right" title="Cambiar posición"><li class="icon-list"></li></a>',
                                                                                            "<input type=hidden id='id_actividad' value='" + obj['id_actividad'] + "'><input type=hidden id='id_proceso' value='" + n4 + "'><input type=text name=n1_" + contador + " id=n1_" + contador + " value='" + obj['descripcion'] + "' style='width:95%;font-size:8pt'>",
                                                                                            select + detalleTarea,
                                                                                            obj['unidad'] + '<span style="display:none">' + obj['descripcion'] + "</span>",
                                                                                            obj['puesto']
                                                                                        ]).draw(false);
                                                                                        $("#tarea_" + contador).chosen();
                                                                                        $("#tarea_" + contador + "_chzn").css('font-size', '7.5pt');
                                                                                        $("#tarea_" + contador + "_chzn").css('width', '50px');
                                                                                        $("#tarea_" + contador + "_chzn .chzn-drop .chzn-search input").css('width', '50px');

                                                                                        if (obj['tiene_tarea'] == 0) {
                                                                                            $("#capa_tarea_" + contador).hide();
                                                                                        }

                                                                                        $("#unidad_" + contador).chosen();
                                                                                        $("#unidad_" + contador + "_chzn").css('font-size', '7.5pt');
                                                                                        $("#unidad_" + contador + "_chzn").css('width', '230px');
                                                                                        $("#puesto_" + contador).chosen();
                                                                                        $("#puesto_" + contador + "_chzn").css('font-size', '7.5pt');
                                                                                        $("#puesto_" + contador + "_chzn").css('width', '230px');

                                                                                        if (obj['tiene_tarea'] == 1) { //Si tiene tarea
                                                                                            $("#unidad_" + contador + "_chzn").hide();
                                                                                            $("#puesto_" + contador + "_chzn").hide();
                                                                                        }
                                                                                    });

                                                                                    $("#nuevaActividad").show();
                                                                                    $("#grabarActividad").show();
                                                                                    $("#tabla_wrapper").show();

                                                                                }
                                                                            });
                                                                        });



                                                                    }
                                                                });
                                                            } else if (nivel == 3) {

                                                                $.ajax({
                                                                    url: urls.siteUrl + '/admin/procesos/obtener-actividad',
                                                                    data: {
                                                                        proceso: n3,
                                                                        nivel: nivel
                                                                    },
                                                                    type: 'post',
                                                                    dataType: 'json',
                                                                    success: function (result) {
                                                                        var contador = 0;
                                                                        //Primero validar que se obtenga data
                                                                        if (result == '' || result == []) {
                                                                            $('#tabla').DataTable().clear().draw();
                                                                            $("#tabla_wrapper").show();
                                                                            $("#nuevaActividad").show();
                                                                            $("#grabarActividad").show();
                                                                            return false;
                                                                        }
                                                                        $('#tabla').DataTable().clear().draw();
                                                                        $.each(result, function (key, obj) {
                                                                            contador++;
                                                                            //Evaluar si tiene tarea
                                                                            var select = '<div style="float:left"><select id=tarea_' + contador + '>';
                                                                            if (obj['tiene_tarea'] == 1) { //Si tiene tarea
                                                                                select += '<option value=1 selected>Sí</option>';
                                                                                select += '<option value=0>No</option>';

                                                                            } else {
                                                                                select += '<option value=1>Sí</option>';
                                                                                select += '<option value=0 selected>No</option>';
                                                                            }

                                                                            select += '</select></div>';
                                                                            var detalleTarea = '<div id="capa_tarea_' + contador + '" style="float:left">\n\
                        <a id="nuevaTarea_' + contador + '" id_actividad=' + obj['id_actividad'] + ' nom_act="' + obj['descripcion'] + '" role="button" class="btn btn-default btn-xs tip-right" title="Ver/Añadir tareas" \n\
                        ><li class="icon-list"></li></a></div>';
                                                                            $('#tabla').DataTable().row.add([
                                                                                contador + " " + '<a id="editPosicion_' + contador + '"' + " posicion=" + obj['codigo_actividad'] + " id_proceso=" + n3 + " id_actividad='" + obj['id_actividad'] + "' nom_act='" + obj['descripcion'] + "'" + ' role="button" class="btn btn-default btn-xs tip-right" title="Cambiar posición"><li class="icon-list"></li></a>',
                                                                                "<input type=hidden id='id_actividad' value='" + obj['id_actividad'] + "'><input type=hidden id='id_proceso' value='" + n3 + "'><input type=text name=n1_" + contador + " id=n1_" + contador + " value='" + obj['descripcion'] + "' style='width:95%;font-size:8pt'>",
                                                                                select + detalleTarea,
                                                                                obj['unidad'] + '<span style="display:none">' + obj['descripcion'] + "</span>",
                                                                                obj['puesto']
                                                                            ]).draw(false);
                                                                            $("#tarea_" + contador).chosen();
                                                                            $("#tarea_" + contador + "_chzn").css('font-size', '7.5pt');
                                                                            $("#tarea_" + contador + "_chzn").css('width', '50px');
                                                                            $("#tarea_" + contador + "_chzn .chzn-drop .chzn-search input").css('width', '50px');

                                                                            if (obj['tiene_tarea'] == 0) {
                                                                                $("#capa_tarea_" + contador).hide();
                                                                            }

                                                                            $("#unidad_" + contador).chosen();
                                                                            $("#unidad_" + contador + "_chzn").css('font-size', '7.5pt');
                                                                            $("#unidad_" + contador + "_chzn").css('width', '230px');
                                                                            $("#puesto_" + contador).chosen();
                                                                            $("#puesto_" + contador + "_chzn").css('font-size', '7.5pt');
                                                                            $("#puesto_" + contador + "_chzn").css('width', '230px');

                                                                            if (obj['tiene_tarea'] == 1) { //Si tiene tarea
                                                                                $("#unidad_" + contador + "_chzn").hide();
                                                                                $("#puesto_" + contador + "_chzn").hide();
                                                                            }
                                                                        });

                                                                        $("#nuevaActividad").show();
                                                                        $("#grabarActividad").show();
                                                                        $("#tabla_wrapper").show();
                                                                    }
                                                                });
                                                            }
                                                        });
                                                    }
                                                });
                                            } else if (nivel == 2) {

                                                $.ajax({
                                                    url: urls.siteUrl + '/admin/procesos/obtener-actividad',
                                                    data: {
                                                        proceso: n2,
                                                        nivel: nivel
                                                    },
                                                    type: 'post',
                                                    dataType: 'json',
                                                    success: function (result) {
                                                        var contador = 0;
                                                        //Primero validar que se obtenga data
                                                        if (result == '' || result == []) {
                                                            $('#tabla').DataTable().clear().draw();
                                                            $("#tabla_wrapper").show();
                                                            $("#nuevaActividad").show();
                                                            $("#grabarActividad").show();
                                                            return false;
                                                        }
                                                        $('#tabla').DataTable().clear().draw();
                                                        $.each(result, function (key, obj) {
                                                            contador++;

                                                            //Evaluar si tiene tarea
                                                            var select = '<div style="float:left"><select id=tarea_' + contador + '>';
                                                            if (obj['tiene_tarea'] == 1) { //Si tiene tarea
                                                                select += '<option value=1 selected>Sí</option>';
                                                                select += '<option value=0>No</option>';

                                                            } else {
                                                                select += '<option value=1>Sí</option>';
                                                                select += '<option value=0 selected>No</option>';
                                                            }

                                                            select += '</select></div>';
                                                            var detalleTarea = '<div id="capa_tarea_' + contador + '" style="float:left">\n\
                        <a id="nuevaTarea_' + contador + '" id_actividad=' + obj['id_actividad'] + ' nom_act="' + obj['descripcion'] + '" role="button" class="btn btn-default btn-xs tip-right" title="Ver/Añadir tareas" \n\
                        ><li class="icon-list"></li></a></div>';
                                                            $('#tabla').DataTable().row.add([
                                                                contador + " " + '<a id="editPosicion_' + contador + '"' + " posicion=" + obj['codigo_actividad'] + " id_proceso=" + n2 + " id_actividad='" + obj['id_actividad'] + "' nom_act='" + obj['descripcion'] + "'" + ' role="button" class="btn btn-default btn-xs tip-right" title="Cambiar posición"><li class="icon-list"></li></a>',
                                                                "<input type=hidden id='id_actividad' value='" + obj['id_actividad'] + "'><input type=hidden id='id_proceso' value='" + n2 + "'><input type=text name=n1_" + contador + " id=n1_" + contador + " value='" + obj['descripcion'] + "' style='width:95%;font-size:8pt'>",
                                                                select + detalleTarea,
                                                                obj['unidad'] + '<span style="display:none">' + obj['descripcion'] + "</span>",
                                                                obj['puesto']
                                                            ]).draw(false);
                                                            $("#tarea_" + contador).chosen();
                                                            $("#tarea_" + contador + "_chzn").css('font-size', '7.5pt');
                                                            $("#tarea_" + contador + "_chzn").css('width', '50px');
                                                            $("#tarea_" + contador + "_chzn .chzn-drop .chzn-search input").css('width', '50px');

                                                            if (obj['tiene_tarea'] == 0) {
                                                                $("#capa_tarea_" + contador).hide();
                                                            }

                                                            $("#unidad_" + contador).chosen();
                                                            $("#unidad_" + contador + "_chzn").css('font-size', '7.5pt');
                                                            $("#unidad_" + contador + "_chzn").css('width', '230px');
                                                            $("#puesto_" + contador).chosen();
                                                            $("#puesto_" + contador + "_chzn").css('font-size', '7.5pt');
                                                            $("#puesto_" + contador + "_chzn").css('width', '230px');

                                                            if (obj['tiene_tarea'] == 1) { //Si tiene tarea
                                                                $("#unidad_" + contador + "_chzn").hide();
                                                                $("#puesto_" + contador + "_chzn").hide();
                                                            }
                                                        });

                                                        $("#nuevaActividad").show();
                                                        $("#grabarActividad").show();
                                                        $("#tabla_wrapper").show();
                                                    }
                                                });
                                            }
                                        });
                                    }
                                }
                            });
                        } else if (nivel == 1) {
                            $.ajax({
                                url: urls.siteUrl + '/admin/procesos/obtener-actividad',
                                data: {
                                    proceso: n1,
                                    nivel: nivel
                                },
                                type: 'post',
                                dataType: 'json',
                                success: function (result) {
                                    var contador = 0;
                                    $("#n2").empty().append("<option value=''>[Proceso nivel 2]</option>");
                                    $("#n2_chzn .chzn-results").empty().append('<li id="n2_chzn_o_0" class="active-result" style="">[Proceso nivel 2]</li>');
                                    $("#n2_chzn a span").empty().append('[Proceso nivel 2]');
                                    $("#n3").empty().append("<option value=''>[Proceso nivel 3]</option>");
                                    $("#n3_chzn .chzn-results").empty().append('<li id="n3_chzn_o_0" class="active-result" style="">[Proceso nivel 3]</li>');
                                    $("#n3_chzn a span").empty().append('[Proceso nivel 3]');
                                    //Primero validar que se obtenga data
                                    if (result == '' || result == []) {
                                        $('#tabla').DataTable().clear().draw();
                                        $("#tabla_wrapper").show();
                                        $("#nuevaActividad").show();
                                        $("#grabarActividad").show();
                                        return false;
                                    }
                                    $('#tabla').DataTable().clear().draw();
                                    $.each(result, function (key, obj) {
                                        contador++;

                                        //Evaluar si tiene tarea
                                        select = '<div style="float:left"><select id=tarea_' + contador + '>';
                                        if (obj['tiene_tarea'] == 1) { //Si tiene tarea
                                            select += '<option value=1 selected>Sí</option>';
                                            select += '<option value=0>No</option>';

                                        } else {
                                            select += '<option value=1>Sí</option>';
                                            select += '<option value=0 selected>No</option>';
                                        }

                                        select += '</select></div>';
                                        detalleTarea = '<div id="capa_tarea_' + contador + '" style="float:left">\n\
                        <a id="nuevaTarea_' + contador + '" id_actividad=' + obj['id_actividad'] + ' nom_act="' + obj['descripcion'] + '" role="button" class="btn btn-default btn-xs tip-right" title="Ver/Añadir tareas" \n\
                        ><li class="icon-list"></li></a></div>';
                                        $('#tabla').DataTable().row.add([
                                            contador + " " + '<a id="editPosicion_' + contador + '"' + " posicion=" + obj['codigo_actividad'] + " id_proceso=" + n1 + " id_actividad='" + obj['id_actividad'] + "' nom_act='" + obj['descripcion'] + "'" + ' role="button" class="btn btn-default btn-xs tip-right" title="Cambiar posición"><li class="icon-list"></li></a>',
                                            "<input type=hidden id='id_actividad' value='" + obj['id_actividad'] + "'><input type=hidden id='id_proceso' value='" + n1 + "'><input type=text name=n1_" + contador + " id=n1_" + contador + " value='" + obj['descripcion'] + "' style='width:95%;font-size:8pt'>",
                                            select + detalleTarea,
                                            obj['unidad'] + '<span style="display:none">' + obj['descripcion'] + "</span>",
                                            obj['puesto']
                                        ]).draw(false);
                                        $("#tarea_" + contador).chosen();
                                        $("#tarea_" + contador + "_chzn").css('font-size', '7.5pt');
                                        $("#tarea_" + contador + "_chzn").css('width', '50px');
                                        $("#tarea_" + contador + "_chzn .chzn-drop .chzn-search input").css('width', '50px');

                                        if (obj['tiene_tarea'] == 0) {
                                            $("#capa_tarea_" + contador).hide();
                                        }

                                        $("#unidad_" + contador).chosen();
                                        $("#unidad_" + contador + "_chzn").css('font-size', '7.5pt');
                                        $("#unidad_" + contador + "_chzn").css('width', '230px');
                                        $("#puesto_" + contador).chosen();
                                        $("#puesto_" + contador + "_chzn").css('font-size', '7.5pt');
                                        $("#puesto_" + contador + "_chzn").css('width', '230px');

                                        if (obj['tiene_tarea'] == 1) { //Si tiene tarea
                                            $("#unidad_" + contador + "_chzn").hide();
                                            $("#puesto_" + contador + "_chzn").hide();
                                        }
                                    });
                                    $("#nuevaActividad").show();
                                    $("#grabarActividad").show();
                                    $("#tabla_wrapper").show();
                                }
                            });
                        }
                    });
                    //fin
                }
            });
        }
    });
});
