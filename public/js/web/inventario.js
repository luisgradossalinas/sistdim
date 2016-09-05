var codigo = 0;
var sentencia_crud = '';
$(document).ready(function () {


    var ocultarSelect = function() {
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

        setearListas();
        if ($(this).val() == '') {
            ocultarSelect();
        } else if ($(this).val() == 0) {
            $("#n0_chzn").show();
            $("#n1_chzn").hide();
            $("#n2_chzn").hide();
            $("#n3_chzn").hide();
            $("#n4_chzn").hide();
            $("#nuevoProceso").show();
            $("#grabarProceso").show();
        } else if ($(this).val() == 1) {
            $("#n0_chzn").show();
            $("#n1_chzn").show();
            $("#n2_chzn").hide();
            $("#n3_chzn").hide();
            $("#n4_chzn").hide();
            $("#nuevoProceso").show();
            $("#grabarProceso").show();
        } else if ($(this).val() == 2) {
            $("#n0_chzn").show();
            $("#n1_chzn").show();
            $("#n2_chzn").show();
            $("#n3_chzn").hide();
            $("#n4_chzn").hide();
            $("#nuevoProceso").show();
            $("#grabarProceso").show();
        } else if ($(this).val() == 3) {
            $("#n0_chzn").show();
            $("#n1_chzn").show();
            $("#n2_chzn").show();
            $("#n3_chzn").show();
            $("#n4_chzn").hide();
            $("#nuevoProceso").show();
            $("#grabarProceso").show();
        } else if ($(this).val() == 4) {
            $("#n0_chzn").show();
            $("#n1_chzn").show();
            $("#n2_chzn").show();
            $("#n3_chzn").show();
            $("#n4_chzn").show();
            $("#nuevoProceso").show();
            $("#grabarProceso").show();
        }
    });
    
    var setearListas = function() {
        
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
    
    $("#n0").change(function(){
        
        var n0 = $("#n0").val();
        var nivel = parseInt($("#nivel").val());
        //Si no se ha seleccionado proceso y es nivel 0, 
        //no ejecutar ajax
        if (n0 == '' || nivel == 0) {
            return false 
        }

        $.ajax({
            url: urls.siteUrl + '/admin/procesos/obtener-procesos1',
            data:{n0:n0},
            type: 'post',
            dataType: 'json',
            success: function (result) {
                var contador = 0;
                $("#n1").empty().append("<option value=''>[Proceso nivel 1]</option>");
                $("#n1_chzn .chzn-results").empty().append('<li id="n1_chzn_o_0" class="active-result result-selected" style="">[Proceso nivel 1]</li>');
                $("#n1_chzn a span").empty().append('[Proceso nivel 1]');
                $.each(result, function (key, obj) {
                    contador++;
                    $("#n1").append("<option value='" + obj['id_proceso_n1'] + "'>" + obj['descripcion'] + "</option>");
                    $("#n1_chzn .chzn-results").append('<li id="n1_chzn_o_'+contador+'" class="active-result" style="">'+obj['descripcion']+'</li>');
                });
            }
        });
    });
    
    
    $("#n1").change(function(){
        
        var n1 = $("#n1").val();
        var nivel = parseInt($("#nivel").val());
        //Si no se ha seleccionado proceso y es nivel 0, 
        //no ejecutar ajax
        if (n1 == '' || nivel == 1) {
            return false 
        }
        
        $.ajax({
            url: urls.siteUrl + '/admin/procesos/obtener-procesos2',
            data:{n1:n1},
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
                    $("#n2_chzn .chzn-results").append('<li id="n2_chzn_o_'+contador+'" class="active-result" style="">'+obj['descripcion']+'</li>');
                });
            }
        });
    });
    
    $("#n2").change(function(){
        
        var n2 = $("#n2").val();
        var nivel = parseInt($("#nivel").val());
        //Si no se ha seleccionado proceso y es nivel 0, 
        //no ejecutar ajax
        if (n2 == '' || nivel == 2) {
            return false 
        }
        
        $.ajax({
            url: urls.siteUrl + '/admin/procesos/obtener-procesos3',
            data:{n2:n2},
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
                    $("#n3_chzn .chzn-results").append('<li id="n3_chzn_o_'+contador+'" class="active-result" style="">'+obj['descripcion']+'</li>');
                });
            }
        });
    });
    
    $("#n3").change(function(){
        
        var n3 = $("#n3").val();
        var nivel = parseInt($("#nivel").val());
        //Si no se ha seleccionado proceso y es nivel 0, 
        //no ejecutar ajax
        if (n3 == '' || nivel == 3) {
            return false 
        }
        
        $.ajax({
            url: urls.siteUrl + '/admin/procesos/obtener-procesos4',
            data:{n3:n3},
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
                    $("#n4_chzn .chzn-results").append('<li id="n4_chzn_o_'+contador+'" class="active-result" style="">'+obj['descripcion']+'</li>');
                });
            }
        });
    });
    





});
