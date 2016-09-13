var sentencia_crud = '';
$(document).ready(function () {

    //Ocultar tabla
    $("#tabla_wrapper").hide();


    //Grabar proceso
    $("#grabarProceso").click(function () {

        var nivel = $("#nivel").val();
        var numReg = $('#tabla').DataTable().data().count() / 7;
        var dataProceso = new Array();
        var contador = 0;
        var mensaje = '';
        var mostrarMensaje = 0;

        //Variables para guardar
        var id_proceso;
        var tipo;
        var nombre;

        var nivel0;
        var nivel0_nombre = $("#n0 option:selected").text();
        var nivel1;
        var nivel1_nombre = $("#n1 option:selected").text();
        var nivel2;
        var nivel2_nombre = $("#n2 option:selected").text();
        var nivel3;
        var nivel3_nombre = $("#n3 option:selected").text();
        var nivel4;
        var nivel4_nombre = $("#n4 option:selected").text();

        if (nivel == 0 || nivel == 1) {
            alert("Para agregar actividades debe elegir un proceso mayor al nivel 1");
            return false;
        }

        if ($('#tabla').DataTable().data().count() == 0) {
            alert('No existen procesos para grabar');
            return false;
        }

        //Recorrer toda la tabla
        $("#tabla tbody tr").each(function () {
            contador++;

            if (nivel == 1) {
                nivel0 = $(this).find("td input").eq(0).val();
                nivel1 = $(this).find("td input").eq(1).val();
                nombre = $(this).find("td input").eq(2).val();
                dataProceso.push(nivel1 + "|" + nivel0 + "|" + nombre);
                if (nombre == '') {
                    mensaje += "En la fila " + contador + ": Debe completar todos los campos \n";
                    mostrarMensaje = 1;
                }
            } else if (nivel == 2) {
                nivel1 = $(this).find("td input").eq(0).val();
                nivel2 = $(this).find("td input").eq(1).val();
                nombre = $(this).find("td input").eq(2).val();
                dataProceso.push(nivel2 + "|" + nivel1 + "|" + nombre);
                if (nombre == '') {
                    mensaje += "En la fila " + contador + ": Debe completar todos los campos \n";
                    mostrarMensaje = 1;
                }
            } else if (nivel == 3) {
                nivel2 = $(this).find("td input").eq(0).val();
                nivel3 = $(this).find("td input").eq(1).val();
                nombre = $(this).find("td input").eq(2).val();
                dataProceso.push(nivel3 + "|" + nivel2 + "|" + nombre);
                if (nombre == '') {
                    mensaje += "En la fila " + contador + ": Debe completar todos los campos \n";
                    mostrarMensaje = 1;
                }
            } else if (nivel == 4) {
                nivel3 = $(this).find("td input").eq(0).val();
                nivel4 = $(this).find("td input").eq(1).val();
                nombre = $(this).find("td input").eq(2).val();
                dataProceso.push(nivel4 + "|" + nivel3 + "|" + nombre);
                if (nombre == '') {
                    mensaje += "En la fila " + contador + ": Debe completar todos los campos \n";
                    mostrarMensaje = 1;
                }
            }
        });

        //Mostrar mensaje si existen datos por completar
        if (mostrarMensaje == 1) {
            alert(mensaje);
            return false;
        }

        if (nivel == 2) {
            $.ajax({
                url: urls.siteUrl + '/admin/procesos/grabar-actividad',
                data: {
                    actividad: dataProceso,
                    n: 2
                },
                type: 'post',
                dataType: 'json',
                success: function (result) {
                    alert(result);
                    $.ajax({
                        url: urls.siteUrl + '/admin/procesos/obtener-procesos2',
                        data: {n1: nivel1},
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
                                alert('No existen registros.');
                                $('#tabla').DataTable().clear().draw();
                                $("#tabla_wrapper").show();
                                $("#nuevoProceso").show();
                                $("#grabarProceso").show();
                                return false;
                            }

                            $('#tabla').DataTable().clear().draw();

                            if (nivel == 2) {
                                $.each(result, function (key, obj) {
                                    contador++;
                                    $('#tabla').DataTable().row.add([
                                        "<center>" + contador + "</center>",
                                        '',
                                        nivel0_nombre,
                                        "<input type=hidden name=id_proceso_n1 value='" + nivel1 + "'>" + nivel1_nombre,
                                        "<input type=hidden name=id_proceso_n2 value='" + obj['id_proceso_n2'] + "'><input type=text name=n2_" + contador + " id=n2_" + contador + " value='" + obj['descripcion'] + "' style='width:95%'>",
                                        '',
                                        ''
                                    ]).draw(false);
                                });
                                $("#nuevoProceso").show();
                                $("#grabarProceso").show();
                                $("#tabla_wrapper").show();
                            }
                        }
                    });
                }
            });
        } else if (nivel == 3) {
            $.ajax({
                url: urls.siteUrl + '/admin/procesos/grabar-procesos',
                data: {
                    procesos: dataProceso,
                    n: 3
                },
                type: 'post',
                dataType: 'json',
                success: function (result) {
                    alert(result);
                    $.ajax({
                        url: urls.siteUrl + '/admin/procesos/obtener-procesos3',
                        data: {n2: nivel2},
                        type: 'post',
                        dataType: 'json',
                        success: function (result) {
                            var contador = 0;
                            $("#n3").empty().append("<option value=''>[Proceso nivel 3]</option>");
                            $("#n3_chzn .chzn-results").empty().append('<li id="n3_chzn_o_0" class="active-result" style="">[Proceso nivel 3]</li>');
                            $("#n3_chzn a span").empty().append('[Proceso nivel 3]');
                            //Primero validar que se obtenga data
                            if (result == '' || result == []) {
                                alert('No existen registros.');
                                $('#tabla').DataTable().clear().draw();
                                $("#tabla_wrapper").show();
                                $("#nuevoProceso").show();
                                $("#grabarProceso").show();
                                return false;
                            }
                            $('#tabla').DataTable().clear().draw();

                            if (nivel == 3) {
                                $.each(result, function (key, obj) {
                                    contador++;
                                    $('#tabla').DataTable().row.add([
                                        "<center>" + contador + "</center>",
                                        '',
                                        nivel0_nombre,
                                        nivel1_nombre,
                                        "<input type=hidden name=id_proceso_n2 value='" + nivel2 + "'>" + nivel2_nombre,
                                        "<input type=hidden name=id_proceso_n3 value='" + obj['id_proceso_n3'] + "'><input type=text name=n3_" + contador + " id=n3_" + contador + " value='" + obj['descripcion'] + "' style='width:95%'>",
                                        ''
                                    ]).draw(false);
                                });
                                $("#tabla_wrapper").show();
                                $("#nuevoProceso").show();
                                $("#grabarProceso").show();
                            }
                        }
                    });
                }
            });
        } else if (nivel == 4) {
            $.ajax({
                url: urls.siteUrl + '/admin/procesos/grabar-procesos',
                data: {
                    procesos: dataProceso,
                    n: 4
                },
                type: 'post',
                dataType: 'json',
                success: function (result) {
                    alert(result);
                    return false;
                    $.ajax({
                        url: urls.siteUrl + '/admin/procesos/obtener-procesos4',
                        data: {n3: nivel3},
                        type: 'post',
                        dataType: 'json',
                        success: function (result) {
                            var contador = 0;
                            //Primero validar que se obtenga data
                            if (result == '' || result == []) {
                                alert('No existen registros.');
                                $('#tabla').DataTable().clear().draw();
                                $("#tabla_wrapper").show();
                                $("#nuevoProceso").show();
                                $("#grabarProceso").show();
                                return false;
                            }
                            $('#tabla').DataTable().clear().draw();
                            $.each(result, function (key, obj) {
                                contador++;
                                $('#tabla').DataTable().row.add([
                                    "<center>" + contador + "</center>",
                                    '',
                                    nivel0_nombre,
                                    nivel1_nombre,
                                    "<input type=hidden name=id_proceso_n2 value='" + nivel2 + "'>" + nivel2_nombre,
                                    "<input type=hidden name=id_proceso_n3 value='" + obj['id_proceso_n3'] + "'><input type=text name=n3_" + contador + " id=n3_" + contador + " value='" + obj['descripcion'] + "' style='width:95%'>",
                                    ''
                                ]).draw(false);
                            });
                            $("#tabla_wrapper").show();
                            $("#nuevoProceso").show();
                            $("#grabarProceso").show();

                        }
                    });
                }
            });
        }

        console.log(dataProceso);


    });


    //Nuevo proceso
    $("#nuevoProceso").click(function () {

        var nivel = $("#nivel").val();
        var n0 = $("#n0").val();
        var n0_nom = $("#n0 option:selected").text();
        var n1 = $("#n1").val();
        var n1_nom = $("#n1 option:selected").text();
        var n2 = $("#n2").val();
        var n2_nom = $("#n2 option:selected").text();
        var n3 = $("#n3").val();
        var n3_nom = $("#n3 option:selected").text();

        if (nivel == '') {
            $("#tabla_wrapper").hide();
            $('#tabla').DataTable().clear().draw();
            return false;
        }

        var nColumnas = 2 + parseInt(nivel);
        var numReg = ($('#tabla').DataTable().data().count() / 7) + 1;

        if (nivel == 0) {
            $('#tabla').DataTable().row.add([
                "<center>" + numReg + "</center>",
                "<select style='width:100%' id=tipoproceso_" + numReg + " name=tipoproceso_" + numReg + "><option value=''>[Tipo]</option></select>",
                "<input type=hidden name=id_proceso value=0><input type=text name=n0_" + numReg + " id=n0_" + numReg + " style='width:90%'>",
                "<input type=text name=n1_" + numReg + " id=n1_" + numReg + ">",
                "<input type=text name=n2_" + numReg + " id=n2_" + numReg + ">",
                "<input type=text name=n3_" + numReg + " id=n3_" + numReg + ">",
                "<input type=text name=n4_" + numReg + " id=n4_" + numReg + ">"
            ]).draw(false);

            $.ajax({
                url: urls.siteUrl + '/admin/procesos/obtener-tipo-proceso',
                type: 'post',
                dataType: 'json',
                success: function (result) {
                    $.each(result, function (key, obj) {
                        $("#tipoproceso_" + numReg).append("<option value='" + obj['codigo_tipoproceso'] + "'>" + obj['descripcion'] + "</option>");
                    });
                    $("#tabla_wrapper").show();
                }
            });
        } else if (nivel == 1) {

            $('#tabla').DataTable().row.add([
                "<center>" + numReg + "</center>",
                "",
                "<input type=hidden name=id_proceso_n0 value='" + n0 + "'>" + n0_nom,
                "<input type=hidden name=id_proceso_n1 value='0'><input type=text name=n1_" + numReg + " id=n2_" + numReg + " style='width:95%'>",
                "<input type=text name=n2_" + numReg + " id=n3_" + numReg + ">",
                "<input type=text name=n3_" + numReg + " id=n3_" + numReg + ">",
                "<input type=text name=n4_" + numReg + " id=n4_" + numReg + ">"
            ]).draw(false);
        } else if (nivel == 2) {
            $('#tabla').DataTable().row.add([
                "<center>" + numReg + "</center>",
                "",
                n0_nom,
                "<input type=hidden name=id_proceso_n1 value='" + n1 + "'>" + n1_nom,
                "<input type=hidden name=id_proceso_n2 value='0'><input type=text name=n2_" + numReg + " id=n2_" + numReg + " style='width:95%'>",
                "<input type=text name=n3_" + numReg + " id=n4_" + numReg + ">",
                "<input type=text name=n4_" + numReg + " id=n5_" + numReg + ">"
            ]).draw(false);
        } else if (nivel == 3) {
            $('#tabla').DataTable().row.add([
                "<center>" + numReg + "</center>",
                "",
                n0_nom,
                n1_nom,
                "<input type=hidden name=id_proceso_n2 value='" + n2 + "'>" + n2_nom,
                "<input type=hidden name=id_proceso_n3 value='0'><input type=text name=n3_" + numReg + " id=n3_" + numReg + " style='width:95%'>",
                "<input type=text name=n4_" + numReg + " id=n4_" + numReg + ">"
            ]).draw(false);
        } else if (nivel == 4) {
            $('#tabla').DataTable().row.add([
                "<center>" + numReg + "</center>",
                "",
                n0_nom,
                n1_nom,
                n2_nom,
                "<input type=hidden name=id_proceso_n3 value='" + n3 + "'>" + n3_nom,
                "<input type=hidden name=id_proceso_n4 value='0'><input type=text name=n4_" + numReg + " id=n4_" + numReg + ">"
            ]).draw(false);
        }
    });

    var ocultarSelect = function () {
        $("#n0_chzn").hide();
        $("#n1_chzn").hide();
        $("#n2_chzn").hide();
        $("#n3_chzn").hide();
        $("#n4_chzn").hide();
        $("#nuevoProceso").hide();
        $("#grabarProceso").hide();
    };

    ocultarSelect();

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
            $("#nuevoProceso").hide();
            $("#grabarProceso").hide();

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

            $("#nuevoProceso").hide();
            $("#grabarProceso").hide();
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
            $("#nuevoProceso").hide();
            $("#grabarProceso").hide();
            $("#tabla_wrapper").hide();
            $('#tabla').DataTable().clear().draw();
        } else if (nivel == 3) {
            $("#n0_chzn").show();
            //$("#n1_chzn").show();

            $("#n1_chzn").hide();
            $("#n1").show();
            $("#n2_chzn").hide();
            $("#n2").show();

            $("#n3").show();
            $("#n4").hide();

            $("#n3_chzn").hide();
            $("#n4_chzn").hide();
            $("#nuevoProceso").hide();
            $("#grabarProceso").hide();
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
            $("#nuevoProceso").hide();
            $("#grabarProceso").hide();
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
        $("#nuevoProceso").hide();
        $("#grabarProceso").hide();
        //Si no se ha seleccionado proceso y es nivel 0, 
        if (n0 == '' || nivel == 0) {
            return false
        }

        if (nivel >= 1) {
            $.ajax({
                url: urls.siteUrl + '/admin/procesos/obtener-procesos1',
                data: {n0: n0},
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
                        alert('No existen registros.');
                        $('#tabla').DataTable().clear().draw();
                        $("#tabla_wrapper").show();
                        $("#nuevoProceso").show();
                        $("#grabarProceso").show();
                        return false;
                    }

                    $('#tabla').DataTable().clear().draw();

                    if (nivel >= 1) {
                        $.each(result, function (key, obj) {
                            contador++;
                            $("#n1").append("<option value='" + obj['id_proceso_n1'] + "'>" + obj['descripcion'] + "</option>");
                            $("#n1_chzn .chzn-drop .chzn-results").append('<li id="n1_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');
                            //$("#n1_chzn .chzn-drop .chzn-results").append('<li id="n1_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');

                        });
                    }
                }
            });
        }
    });

    $("#n1").change(function () {

        var n0 = $("#n0").val();
        var n1 = $("#n1").val();
        var nom_n0 = $("#n0 option:selected").text();
        var nom_n1 = $("#n1 option:selected").text();
        var nivel = parseInt($("#nivel").val());
        $('#tabla').DataTable().clear().draw();
        $("#tabla_wrapper").hide();
        $("#nuevoProceso").hide();
        $("#grabarProceso").hide();

        //Si no se ha seleccionado proceso y es nivel 0, 
        //no ejecutar ajax
        if (n1 == '' || nivel == '') {
            return false;
        }

        $.ajax({
            url: urls.siteUrl + '/admin/procesos/obtener-procesos2',
            data: {n1: n1},
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
                    alert('No existen registros.');
                    if (nivel == 1) {
                        $('#tabla').DataTable().clear().draw();
                        $("#tabla_wrapper").show();
                        $("#nuevoProceso").show();
                        $("#grabarProceso").show();
                    }
                    return false;
                }

                $('#tabla').DataTable().clear().draw();

                if (nivel == 1) {
                    $.each(result, function (key, obj) {
                        contador++;
                        $('#tabla').DataTable().row.add([
                            "<center>" + contador + "</center>",
                            '',
                            nom_n0,
                            "<input type=hidden name=id_proceso_n1 value='" + n1 + "'>" + nom_n1,
                            "<input type=hidden name=id_proceso_n2 value='" + obj['id_proceso_n2'] + "'><input type=text name=n2_" + contador + " id=n2_" + contador + " value='" + obj['descripcion'] + "' style='width:95%'>",
                            '',
                            ''
                        ]).draw(false);
                    });
                    $("#nuevoProceso").show();
                    $("#grabarProceso").show();
                    $("#tabla_wrapper").show();
                }

                if (nivel >= 2) {
                    $.each(result, function (key, obj) {
                        contador++;
                        $("#n2").append("<option value='" + obj['id_proceso_n2'] + "'>" + obj['descripcion'] + "</option>");
                        $("#n2_chzn .chzn-results").append('<li id="n2_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');
                    });
                    $("#tabla_wrapper").hide();
                    $("#nuevoProceso").hide();
                    $("#grabarProceso").hide();
                }
            }
        });
    });

    $("#n2").change(function () {

        var n2 = $("#n2").val();
        var nom_n0 = $("#n0 option:selected").text();
        var nom_n1 = $("#n1 option:selected").text();
        var nom_n2 = $("#n2 option:selected").text();
        var nivel = parseInt($("#nivel").val());
        $('#tabla').DataTable().clear().draw();
        $("#tabla_wrapper").hide();
        $("#nuevoProceso").hide();
        $("#grabarProceso").hide();
        //Si no se ha seleccionado proceso y es nivel 0, 
        //no ejecutar ajax
        if (n2 == '' || nivel == '') {
            return false
        }

        $.ajax({
            url: urls.siteUrl + '/admin/procesos/obtener-procesos3',
            data: {n2: n2},
            type: 'post',
            dataType: 'json',
            success: function (result) {

                var contador = 0;
                $("#n3").empty().append("<option value=''>[Proceso nivel 3]</option>");
                $("#n3_chzn .chzn-results").empty().append('<li id="n3_chzn_o_0" class="active-result" style="">[Proceso nivel 3]</li>');
                $("#n3_chzn a span").empty().append('[Proceso nivel 3]');
                //Primero validar que se obtenga data
                if (result == '' || result == []) {
                    alert('No existen registros.');
                    if (nivel == 2) {
                        $('#tabla').DataTable().clear().draw();
                        $("#tabla_wrapper").show();
                        $("#nuevoProceso").show();
                        $("#grabarProceso").show();
                    }
                    return false;
                }
                $('#tabla').DataTable().clear().draw();

                if (nivel == 2) {
                    $.each(result, function (key, obj) {
                        contador++;
                        $('#tabla').DataTable().row.add([
                            "<center>" + contador + "</center>",
                            '',
                            nom_n0,
                            nom_n1,
                            "<input type=hidden name=id_proceso_n2 value='" + n2 + "'>" + nom_n2,
                            "<input type=hidden name=id_proceso_n3 value='" + obj['id_proceso_n3'] + "'><input type=text name=n3_" + contador + " id=n3_" + contador + " value='" + obj['descripcion'] + "' style='width:95%'>",
                            ''
                        ]).draw(false);
                    });
                    $("#tabla_wrapper").show();
                    $("#nuevoProceso").show();
                    $("#grabarProceso").show();
                }

                if (nivel >= 3) {
                    $.each(result, function (key, obj) {
                        contador++;
                        $("#n3").append("<option value='" + obj['id_proceso_n3'] + "'>" + obj['descripcion'] + "</option>");
                        $("#n3_chzn .chzn-results").append('<li id="n3_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');
                    });
                }
            }
        });
    });

    $("#n3").change(function () {

        var n3 = $("#n3").val();
        var nom_n0 = $("#n0 option:selected").text();
        var nom_n1 = $("#n1 option:selected").text();
        var nom_n2 = $("#n2 option:selected").text();
        var nom_n3 = $("#n3 option:selected").text();
        var nivel = parseInt($("#nivel").val());
        $('#tabla').DataTable().clear().draw();
        $("#tabla_wrapper").hide();
        $("#nuevoProceso").hide();
        $("#grabarProceso").hide();

        //Si no se ha seleccionado proceso y es nivel 0, 
        //no ejecutar ajax
        if (n3 == '' || nivel == '') {
            return false
        }

        $.ajax({
            url: urls.siteUrl + '/admin/procesos/obtener-procesos4',
            data: {n3: n3},
            type: 'post',
            dataType: 'json',
            success: function (result) {

                var contador = 0;
                //Primero validar que se obtenga data
                if (result == '' || result == []) {
                    alert('No existen registros.');
                    if (nivel == 3) {
                        $('#tabla').DataTable().clear().draw();
                        $("#tabla_wrapper").show();
                        $("#nuevoProceso").show();
                        $("#grabarProceso").show();
                    }
                    return false;
                }

                $('#tabla').DataTable().clear().draw();

                if (nivel == 3) {
                    $.each(result, function (key, obj) {
                        contador++;
                        $('#tabla').DataTable().row.add([
                            "<center>" + contador + "</center>",
                            '',
                            nom_n0,
                            nom_n1,
                            nom_n2,
                            "<input type=hidden name=id_proceso_n3 value='" + n3 + "'>" + nom_n3,
                            "<input type=hidden name=id_proceso_n4 value='" + obj['id_proceso_n4'] + "'><input type=text name=n4_" + contador + " id=n4_" + contador + " value='" + obj['descripcion'] + "' style='width:95%'>",
                        ]).draw(false);
                    });
                    $("#tabla_wrapper").show();
                    $("#nuevoProceso").show();
                    $("#grabarProceso").show();
                } else if (nivel >= 4) {
                    $.each(result, function (key, obj) {
                        contador++;
                        $("#n4").append("<option value='" + obj['id_proceso_n4'] + "'>" + obj['descripcion'] + "</option>");
                        $("#n4_chzn .chzn-results").append('<li id="n4_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');
                    });
                }
            }
        });
    });

    $("#n4").change(function () {

        var n4 = $("#n4").val();
        var nom_n0 = $("#n0 option:selected").text();
        var nom_n1 = $("#n1 option:selected").text();
        var nom_n2 = $("#n2 option:selected").text();
        var nom_n3 = $("#n3 option:selected").text();
        var nivel = parseInt($("#nivel").val());
        $('#tabla').DataTable().clear().draw();
        $("#tabla_wrapper").hide();
        $("#nuevoProceso").hide();
        $("#grabarProceso").hide();

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
                    alert('No existen registros.');
                    if (nivel == 4) {
                        $('#tabla').DataTable().clear().draw();
                        $("#tabla_wrapper").show();
                        $("#nuevoProceso").show();
                        $("#grabarProceso").show();
                    }
                    return false;
                }

                $('#tabla').DataTable().clear().draw();

                if (nivel == 3) {
                    $.each(result, function (key, obj) {
                        contador++;
                        $('#tabla').DataTable().row.add([
                            "<center>" + contador + "</center>",
                            '',
                            nom_n0,
                            nom_n1,
                            nom_n2,
                            "<input type=hidden name=id_proceso_n3 value='" + n3 + "'>" + nom_n3,
                            "<input type=hidden name=id_proceso_n4 value='" + obj['id_proceso_n4'] + "'><input type=text name=n4_" + contador + " id=n4_" + contador + " value='" + obj['descripcion'] + "' style='width:95%'>",
                        ]).draw(false);
                    });
                    $("#tabla_wrapper").show();
                    $("#nuevoProceso").show();
                    $("#grabarProceso").show();
                } else if (nivel >= 4) {
                    $.each(result, function (key, obj) {
                        contador++;
                        $("#n4").append("<option value='" + obj['id_proceso_n4'] + "'>" + obj['descripcion'] + "</option>");
                        $("#n4_chzn .chzn-results").append('<li id="n4_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');
                    });
                }
            }
        });
    });

});
