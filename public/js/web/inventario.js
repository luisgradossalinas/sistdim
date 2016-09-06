var codigo = 0;
var sentencia_crud = '';
$(document).ready(function () {

    //Ocultar tabla
    $("#tabla_wrapper").hide();

    //Nuevo proceso
    $("#nuevoProceso").click(function () {

        var nivel = $("#nivel").val();

        if (nivel == '') {
            $("#tabla_wrapper").hide();
            $('#tabla').DataTable().clear();
            return false;
        }

        var nColumnas = 2 + parseInt(nivel);
        var numReg = ($('#tabla').DataTable().data().count() / 7) + 1;


        //Mostrar tablas
        $("#tabla_wrapper").show();

        if (nivel == 0) {
            $('#tabla').DataTable().row.add([
                "<center>" + numReg + "</center>",
                "<select style='width:100%' id=tipoproceso_" + numReg + " name=tipoproceso_" + numReg + "><option value=''>[Tipo]</option></select>",
                "<input type=hidden name=id_puesto value=0><input type=text name=n0_" + numReg + " id=n0_" + numReg + ">",
                "<input type=hidden name=id_puesto value=0><input type=text name=n1_" + numReg + " id=n2_" + numReg + ">",
                "<input type=hidden name=id_puesto value=0><input type=text name=n2_" + numReg + " id=n3_" + numReg + ">",
                "<input type=hidden name=id_puesto value=0><input type=text name=n3_" + numReg + " id=n4_" + numReg + ">",
                "<input type=hidden name=id_puesto value=0><input type=text name=n4_" + numReg + " id=n5_" + numReg + ">"
            ]).draw(false);


        }

        $.ajax({
            url: urls.siteUrl + '/admin/procesos/obtener-tipo-proceso',
            type: 'post',
            dataType: 'json',
            success: function (result) {
                //Llenar tipo
                $.each(result, function (key, obj) {
                    $("#tipoproceso_" + numReg).append("<option value='" + obj['codigo_tipoproceso'] + "'>" + obj['descripcion'] + "</option>");
                });
            }
        });



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
        $('#tabla').DataTable().clear();

        if (nivel == '') {
            ocultarSelect();
            $("#tabla_wrapper").hide();
            $("#tabla").DataTable().column(0).visible(true);
            $("#tabla").DataTable().column(1).visible(true);
            $("#tabla").DataTable().column(2).visible(true);
            $("#tabla").DataTable().column(3).visible(true);
            $("#tabla").DataTable().column(4).visible(true);
            $("#tabla").DataTable().column(5).visible(true);
            $("#tabla").DataTable().column(6).visible(true);
        } else if (nivel == 0) {
            $("#n0_chzn").hide();
            $("#n1_chzn").hide();
            $("#n2_chzn").hide();
            $("#n3_chzn").hide();
            $("#n4_chzn").hide();
            $("#nuevoProceso").show();
            $("#grabarProceso").show();
            $("#tabla").DataTable().column(0).visible(true);
            $("#tabla").DataTable().column(1).visible(true);
            $("#tabla").DataTable().column(2).visible(true);
            $("#tabla").DataTable().column(3).visible(false);
            $("#tabla").DataTable().column(4).visible(false);
            $("#tabla").DataTable().column(5).visible(false);
            $("#tabla").DataTable().column(6).visible(false);
            $('#tabla').DataTable().clear();
            //Obtener los proceso 0
            $.ajax({
                url: urls.siteUrl + '/admin/procesos/obtener-procesos0',
                type: 'post',
                dataType: 'json',
                success: function (result) {
                    var contador = 0;
                    $.each(result, function (key, obj) {
                        contador++;
                        $('#tabla').DataTable().row.add([
                            "<center>" + contador + "</center>",
                            obj['codigo_tipoproceso'],
                            "<input type=hidden name=id_puesto value=0><input type=text name=n0_" + contador + " id=n0_" + contador + " value='" + obj['descripcion'] + "'>",
                            "<input type=hidden name=id_puesto value=0><input type=text name=n1_" + contador + " id=n2_" + contador + ">",
                            "<input type=hidden name=id_puesto value=0><input type=text name=n2_" + contador + " id=n3_" + contador + ">",
                            "<input type=hidden name=id_puesto value=0><input type=text name=n3_" + contador + " id=n4_" + contador + ">",
                            "<input type=hidden name=id_puesto value=0><input type=text name=n4_" + contador + " id=n5_" + contador + ">"
                        ]).draw(false);
                    });
                    $("#tabla_wrapper").show();
                }
            });



        } else if (nivel == 1) {
            $("#n0_chzn").show();
            $("#n1_chzn").hide();
            $("#n2_chzn").hide();
            $("#n3_chzn").hide();
            $("#n4_chzn").hide();
            $("#nuevoProceso").show();
            $("#grabarProceso").show();
            $("#tabla").DataTable().column(0).visible(true);
            $("#tabla").DataTable().column(1).visible(true);
            $("#tabla").DataTable().column(2).visible(true);
            $("#tabla").DataTable().column(3).visible(true);
            $("#tabla").DataTable().column(4).visible(false);
            $("#tabla").DataTable().column(5).visible(false);
            $("#tabla").DataTable().column(6).visible(false);
            $("#tabla_wrapper").hide();
            $('#tabla').DataTable().clear();
        } else if (nivel == 2) {
            $("#n0_chzn").show();
            $("#n1_chzn").show();
            $("#n2_chzn").hide();
            $("#n3_chzn").hide();
            $("#n4_chzn").hide();
            $("#nuevoProceso").show();
            $("#grabarProceso").show();
            $("#tabla").DataTable().column(0).visible(true);
            $("#tabla").DataTable().column(1).visible(true);
            $("#tabla").DataTable().column(2).visible(true);
            $("#tabla").DataTable().column(3).visible(true);
            $("#tabla").DataTable().column(4).visible(true);
            $("#tabla").DataTable().column(5).visible(false);
            $("#tabla").DataTable().column(6).visible(false);
            $("#tabla_wrapper").hide();
            $('#tabla').DataTable().clear();
        } else if (nivel == 3) {
            $("#n0_chzn").show();
            $("#n1_chzn").show();
            $("#n2_chzn").show();
            $("#n3_chzn").hide();
            $("#n4_chzn").hide();
            $("#nuevoProceso").show();
            $("#grabarProceso").show();
            $("#tabla").DataTable().column(0).visible(true);
            $("#tabla").DataTable().column(1).visible(true);
            $("#tabla").DataTable().column(2).visible(true);
            $("#tabla").DataTable().column(3).visible(true);
            $("#tabla").DataTable().column(4).visible(true);
            $("#tabla").DataTable().column(5).visible(true);
            $("#tabla").DataTable().column(5).visible(false);
            $("#tabla_wrapper").hide();
            $('#tabla').DataTable().clear();
        } else if (nivel == 4) {
            $("#n0_chzn").show();
            $("#n1_chzn").show();
            $("#n2_chzn").show();
            $("#n3_chzn").show();
            $("#n4_chzn").hide();
            $("#nuevoProceso").show();
            $("#grabarProceso").show();
            $("#tabla").DataTable().column(0).visible(true);
            $("#tabla").DataTable().column(1).visible(true);
            $("#tabla").DataTable().column(2).visible(true);
            $("#tabla").DataTable().column(3).visible(true);
            $("#tabla").DataTable().column(4).visible(true);
            $("#tabla").DataTable().column(5).visible(true);
            $("#tabla").DataTable().column(6).visible(true);
            $("#tabla_wrapper").hide();
            $('#tabla').DataTable().clear();

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
        var nivel = parseInt($("#nivel").val());
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
                    //Primero validar que se obtenga data
                    if (result == '' || result == []) {
                        alert('No existen registros.');
                        $('#tabla').DataTable().clear().draw();
                        return false;
                    }

                    if (nivel == 1) {

                        $.each(result, function (key, obj) {
                            contador++;
                            $('#tabla').DataTable().row.add([
                                "<center>" + contador + "</center>",
                                'Tipo',
                                "<input type=hidden name=id_puesto value=0><input type=text name=n0_" + contador + " id=n0_" + contador + " value='" + obj['descripcion'] + "' readonly>",
                                "<input type=hidden name=id_puesto value=0><input type=text name=n1_" + contador + " id=n2_" + contador + ">",
                                "<input type=hidden name=id_puesto value=0><input type=text name=n2_" + contador + " id=n3_" + contador + ">",
                                "<input type=hidden name=id_puesto value=0><input type=text name=n3_" + contador + " id=n4_" + contador + ">",
                                "<input type=hidden name=id_puesto value=0><input type=text name=n4_" + contador + " id=n5_" + contador + ">"
                            ]).draw(false);
                        });
                        $("#tabla_wrapper").show();

                    }

                    if (nivel == 2) {
                        $("#n1").empty().append("<option value=''>[Proceso nivel 1]</option>");
                        $("#n1_chzn .chzn-results").empty().append('<li id="n1_chzn_o_0" class="active-result result-selected" style="">[Proceso nivel 1]</li>');
                        $("#n1_chzn a span").empty().append('[Proceso nivel 1]');
                        $.each(result, function (key, obj) {
                            contador++;
                            $("#n1").append("<option value='" + obj['id_proceso_n1'] + "'>" + obj['descripcion'] + "</option>");
                            $("#n1_chzn .chzn-results").append('<li id="n1_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');
                        });
                    }

                }
            });
        }

    });


    $("#n1").change(function () {

        var n1 = $("#n1").val();
        var nivel = parseInt($("#nivel").val());
        //Si no se ha seleccionado proceso y es nivel 0, 
        //no ejecutar ajax
        if (n1 == '' || nivel == 1) {
            return false
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
                $.each(result, function (key, obj) {
                    contador++;
                    $("#n2").append("<option value='" + obj['id_proceso_n2'] + "'>" + obj['descripcion'] + "</option>");
                    $("#n2_chzn .chzn-results").append('<li id="n2_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');
                });
            }
        });
    });

    $("#n2").change(function () {

        var n2 = $("#n2").val();
        var nivel = parseInt($("#nivel").val());
        //Si no se ha seleccionado proceso y es nivel 0, 
        //no ejecutar ajax
        if (n2 == '' || nivel == 2) {
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
                $.each(result, function (key, obj) {
                    contador++;
                    $("#n3").append("<option value='" + obj['id_proceso_n3'] + "'>" + obj['descripcion'] + "</option>");
                    $("#n3_chzn .chzn-results").append('<li id="n3_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');
                });
            }
        });
    });

    $("#n3").change(function () {

        var n3 = $("#n3").val();
        var nivel = parseInt($("#nivel").val());
        //Si no se ha seleccionado proceso y es nivel 0, 
        //no ejecutar ajax
        if (n3 == '' || nivel == 3) {
            return false
        }

        $.ajax({
            url: urls.siteUrl + '/admin/procesos/obtener-procesos4',
            data: {n3: n3},
            type: 'post',
            dataType: 'json',
            success: function (result) {
                var contador = 0;
                $("#n4").empty().append("<option value=''>[Proceso nivel 4]</option>");
                $("#n4_chzn .chzn-results").empty().append('<li id="n4_chzn_o_0" class="active-result" style="">[Proceso nivel 4]</li>');
                $("#n4_chzn a span").empty().append('[Proceso nivel 4]');
                $.each(result, function (key, obj) {
                    contador++;
                    $("#n4").append("<option value='" + obj['id_proceso_n4'] + "'>" + obj['descripcion'] + "</option>");
                    $("#n4_chzn .chzn-results").append('<li id="n4_chzn_o_' + contador + '" class="active-result" style="">' + obj['descripcion'] + '</li>');
                });
            }
        });
    });


});
