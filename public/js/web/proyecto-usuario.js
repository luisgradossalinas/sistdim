$(document).ready(function () {

    $("#usuario").change(function () {
        
        var usuario = $("#usuario").val();
        var proyecto = $("#proyecto").val();

        $('#controla').val(0);
        $('#permisos').empty();

        if (usuario == '') {
            alert("Seleccione un usuario");
            $("#usuario").focus();
            return false;
        }

        if (proyecto == '') {
            $("#proyecto").focus();
            return false;
        }

        $.ajax({
            url: urls.siteUrl + '/admin/proyecto/permiso-usuario',
            data: {usuario: usuario,
                   proyecto: proyecto},
            type: 'post',
            dataType: 'json',
            success: function (result) {

                var html = '';
                var contador = 0;
                $.each(result, function (key, obj) {
                    contador++;
                    html += "<tr>";
                    html += "<td>" + contador + "</td>";
                    html += "<td>" + obj['nombre'] + "</td>";

                    if (obj['estado_permiso'] == 1) {
                        html += "<td><center><input type='checkbox' name='estado_permiso' value="+obj['id_recurso']+" checked></center></td>";
                    } else {
                        html += "<td><center><input type='checkbox' name='estado_permiso' value="+obj['id_recurso']+"></center></td>";
                    }
                    html += "</tr>";
                });

                $('#controla').val(1);
                $('#permisos').empty().html(html);
            }
        })                
    });

    $("#proyecto").change(function () {

        var usuario = $("#usuario").val();
        var proyecto = $("#proyecto").val();

        $('#controla').val(0);
        $('#permisos').empty();

        if (usuario == '') {
            alert("Seleccione un usuario");
            $("#usuario").focus();
            return false;
        }

        if (proyecto == '') {
            $("#proyecto").focus();
            return false;
        }

        $.ajax({
            url: urls.siteUrl + '/admin/proyecto/permiso-usuario',
            data: {usuario: usuario,
                   proyecto: proyecto
               },
            type: 'post',
            dataType: 'json',
            success: function (result) {

                var html = '';
                var contador = 0;
                $.each(result, function (key, obj) {
                    contador++;
                    html += "<tr>";
                    html += "<td><center>" + contador + "</center></td>";
                    html += "<td>" + obj['nombre'] + "</td>";

                    if (obj['estado_permiso'] == 1) {
                        html += "<td><center><input type='checkbox' name='estado_permiso' value="+obj['id_recurso']+" checked></center></td>";
                    } else {
                        html += "<td><center><input type='checkbox' name='estado_permiso' value="+obj['id_recurso']+"></center></td>";
                    }
                    html += "</tr>";
                });

                $('#controla').val(1);
                $('#permisos').empty().html(html);
            }
        })
    });
    
    $("#grabarPermisos").click(function(){
       
       if ($('#controla').val() == 0) {
           alert("No existen datos para grabar");
           return false;
       }
       
       var usuario = $("#usuario").val();
       var proyecto = $("#proyecto").val();
        
        var selectedItems = new Array();
        var NoSelectedItems = new Array();
        var recursosAdd = '';
        var recursosDelete = '';
        $("#permisos tr td input[@name='estado_permiso[]']:checked").each(function(){
                selectedItems.push($(this).val());
                recursosAdd += $(this).val() + ',';
        });

        $("#permisos tr td input[@name='estado_permiso[]']:not(:checked)").each(function(){
            if ($(this).val() != '') {
                NoSelectedItems.push($(this).val());
                recursosDelete += $(this).val() + ',';
            }
                
        });     
        
       //Llamar a ajax
       $.ajax({
            url: urls.siteUrl + '/admin/proyecto/grabar-permisos',
            data: {
                usuario: usuario,
                proyecto: proyecto,
                rec_add:selectedItems,
                rec_del:NoSelectedItems
            },
            type: 'post',
            dataType: 'json',
            success: function (result) {
                //location.reload();
            }
        });
       
       alert("Permisos grabados");
          
    });

})