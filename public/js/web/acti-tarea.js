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
            unidad = $(this).find("td select").eq(0).val();
            puesto = $(this).find("td select").eq(1).val();
            dataActividad.push(id_actividad + "|" + proceso + "|" + actividad + "|" + unidad + "|" + puesto);
            if (actividad == '' || (unidad == '' || unidad == 0) ||
                    (puesto == '' || puesto == 0)) {
                mensaje += "En la fila " + contador + ": Debe completar todos los campos \n";
                mostrarMensaje = 1;
            }
        });

        //Mostrar mensaje si existen datos por completar
        if (mostrarMensaje == 1) {
            alert(mensaje);
            return false;
        }

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
                            $('#tabla').DataTable().row.add([
                                contador,
                                "<input type=hidden id='id_actividad' value='" + obj['id_actividad'] + "'><input type=hidden id='id_proceso' value='" + proceso + "'><input type=text name=n1_" + contador + " id=n1_" + contador + " value='" + obj['descripcion'] + "' style='width:95%;font-size:8pt'>",
                                obj['unidad'],
                                obj['puesto']
                            ]).draw(false);
                            $("#unidad_" + contador).chosen();
                            $("#unidad_" + contador + "_chzn").css('font-size', '8pt');
                            $("#unidad_" + contador + "_chzn").css('width', '250px');
                            $("#puesto_" + contador).chosen();
                            $("#puesto_" + contador + "_chzn").css('font-size', '8pt');
                            $("#puesto_" + contador + "_chzn").css('width', '250px');
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
                    num: num
                },
                type: 'post',
                //dataType: 'json',
                success: function (result) {
                    $("#capa_" + num).empty().append(result);
                    //$("#puesto_" + num).empty().append(result);
                    $("#puesto_" + num).chosen();
                    $("#puesto_" + num + "_chzn").css('font-size', '8pt');
                    $("#puesto_" + num + "_chzn").css('width', '250px');
                }
            });
        }
    });



    //Nueva actividad
    //$("#nuevaActividad1").click(function () {
    nuevaActividad = function () {

        var nivel = $("#nivel").val();
        var n0 = $("#n0").val();
        var n0_nom = $("#n0 option:selected").text();
        var n1 = $("#n1").val();
        var n1_nom = $("#n1 option:selected").text();
        var n2 = $("#n2").val();
        var n2_nom = $("#n2 option:selected").text();
        var n3 = $("#n3").val();
        var n3_nom = $("#n3 option:selected").text();
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

        var numReg = ($('#tabla').DataTable().data().count() / 4) + 1;

//hiden
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
                data: {num: numReg},
                type: 'post',
                success: function (result) {
                    $("#nuevaActividad").attr('onclick','nuevaActividad()');
                    $('#tabla').DataTable().row.add([
                        numReg,
                        "<input type=hidden id='id_actividad' value='0'><input type=hidden id='id_proceso' value='" + proceso + "'><input type=text name=n1_" + numReg + " id=n1_" + numReg + " style='width:95%;font-size:8pt'>",
                        result,
                        "<div id='capa_" + numReg + "'></div>"
                    ]).draw(false);

                    $("#unidad_" + numReg).chosen();
                    $("#unidad_" + numReg + "_chzn").css('font-size', '8pt');
                    $("#unidad_" + numReg + "_chzn").css('width', '250px');
                    $("#capa_" + numReg).empty().append("<select id='puesto_" + numReg + "'><option value=''>[Seleccione puesto]</option></select>");
                    $("#puesto_" + numReg).chosen();
                    $("#puesto_" + numReg + "_chzn").css('font-size', '8pt');
                    $("#puesto_" + numReg + "_chzn").css('width', '250px');
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
            $("#n1_chzn").hide();
            $("#n2_chzn").hide();
            $("#n3_chzn").hide();
            $("#n4_chzn").hide();

            $("#n1").show();
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

            $("#n2").show();
            $("#n3").hide();
            $("#n4").hide();

            $("#n2_chzn").hide();
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
        //$("#tabla_wrapper").hide();
        //$("#tabla_wrapper").hide();
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
        var nom_n0 = $("#n0 option:selected").text();
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
                    //Primero validar que se obtenga data
                    if (result == '' || result == []) {
                        $('#tabla').DataTable().clear().draw();
                        return false;
                    }

                    $('#tabla').DataTable().clear().draw();

                    if (nivel >= 1) {
                        $.each(result, function (key, obj) {
                            contador++;
                            $("#n1").append("<option value='" + obj['id_proceso_n1'] + "'>" + obj['descripcion'] + "</option>");
                            $("#n1_chzn .chzn-drop .chzn-results").append('<li id="n1_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');
                        });
                    }
                }
            });
        }
    });

    $("#n1").change(function () {

        var n0 = $("#n0").val();
        var n1 = $("#n1").val();
        var nivel = parseInt($("#nivel").val());
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
                        return false;
                    }

                    $('#tabla').DataTable().clear().draw();

                    if (nivel >= 2) {
                        $.each(result, function (key, obj) {
                            contador++;
                            $("#n2").append("<option value='" + obj['id_proceso_n2'] + "'>" + obj['descripcion'] + "</option>");
                            $("#n2_chzn .chzn-results").append('<li id="n2_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');
                        });
                        $("#tabla_wrapper").hide();
                        $("#nuevaActividad").hide();
                        $("#grabarActividad").hide();
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
                        $('#tabla').DataTable().row.add([
                            contador,
                            "<input type=hidden id='id_actividad' value='" + obj['id_actividad'] + "'><input type=hidden id='id_proceso' value='" + n1 + "'><input type=text name=n1_" + contador + " id=n1_" + contador + " value='" + obj['descripcion'] + "' style='width:95%;font-size:8pt'>",
                            obj['unidad'],
                            obj['puesto']
                        ]).draw(false);
                        $("#unidad_" + contador).chosen();
                        $("#unidad_" + contador + "_chzn").css('font-size', '8pt');
                        $("#unidad_" + contador + "_chzn").css('width', '250px');
                        $("#puesto_" + contador).chosen();
                        $("#puesto_" + contador + "_chzn").css('font-size', '8pt');
                        $("#puesto_" + contador + "_chzn").css('width', '250px');
                    });


                    $("#nuevaActividad").show();
                    $("#grabarActividad").show();
                    $("#tabla_wrapper").show();
                }
            });
        }
    });

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

                    $.each(result, function (key, obj) {
                        contador++;
                        $("#n3").append("<option value='" + obj['id_proceso_n3'] + "'>" + obj['descripcion'] + "</option>");
                        $("#n3_chzn .chzn-results").append('<li id="n3_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');
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
                        $('#tabla').DataTable().row.add([
                            contador,
                            "<input type=hidden id='id_actividad' value='" + obj['id_actividad'] + "'><input type=hidden id='id_proceso' value='" + n2 + "'><input type=text name=n1_" + contador + " id=n1_" + contador + " value='" + obj['descripcion'] + "' style='width:95%;font-size:8pt'>",
                            obj['unidad'],
                            obj['puesto']
                        ]).draw(false);
                        $("#unidad_" + contador).chosen();
                        $("#unidad_" + contador + "_chzn").css('font-size', '8pt');
                        $("#unidad_" + contador + "_chzn").css('width', '250px');
                        $("#puesto_" + contador).chosen();
                        $("#puesto_" + contador + "_chzn").css('font-size', '8pt');
                        $("#puesto_" + contador + "_chzn").css('width', '250px');
                    });

                    $("#nuevaActividad").show();
                    $("#grabarActividad").show();
                    $("#tabla_wrapper").show();
                }
            });

        }


    });

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

                    $.each(result, function (key, obj) {
                        contador++;
                        $("#n4").append("<option value='" + obj['id_proceso_n4'] + "'>" + obj['descripcion'] + "</option>");
                        $("#n4_chzn .chzn-results").append('<li id="n4_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');
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
                        $('#tabla').DataTable().row.add([
                            contador,
                            "<input type=hidden id='id_actividad' value='" + obj['id_actividad'] + "'><input type=hidden id='id_proceso' value='" + n3 + "'><input type=text name=n1_" + contador + " id=n1_" + contador + " value='" + obj['descripcion'] + "' style='width:95%;font-size:8pt'>",
                            obj['unidad'],
                            obj['puesto']
                        ]).draw(false);
                        $("#unidad_" + contador).chosen();
                        $("#unidad_" + contador + "_chzn").css('font-size', '8pt');
                        $("#unidad_" + contador + "_chzn").css('width', '250px');
                        $("#puesto_" + contador).chosen();
                        $("#puesto_" + contador + "_chzn").css('font-size', '8pt');
                        $("#puesto_" + contador + "_chzn").css('width', '250px');
                    });

                    $("#nuevaActividad").show();
                    $("#grabarActividad").show();
                    $("#tabla_wrapper").show();
                }
            });
        }
    });

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
                    $('#tabla').DataTable().row.add([
                        contador,
                        "<input type=hidden id='id_actividad' value='" + obj['id_actividad'] + "'><input type=hidden id='id_proceso' value='" + n4 + "'><input type=text name=n1_" + contador + " id=n1_" + contador + " value='" + obj['descripcion'] + "' style='width:95%;font-size:8pt'>",
                        obj['unidad'],
                        obj['puesto']
                    ]).draw(false);
                    $("#unidad_" + contador).chosen();
                    $("#unidad_" + contador + "_chzn").css('font-size', '8pt');
                    $("#unidad_" + contador + "_chzn").css('width', '250px');
                    $("#puesto_" + contador).chosen();
                    $("#puesto_" + contador + "_chzn").css('font-size', '8pt');
                    $("#puesto_" + contador + "_chzn").css('width', '250px');
                });

                $("#nuevaActividad").show();
                $("#grabarActividad").show();
                $("#tabla_wrapper").show();
            }
        });
    });

});
