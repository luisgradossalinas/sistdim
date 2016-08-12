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
            data: {usuario: usuario},
            type: 'post',
            dataType: 'json',
            success: function (result) {

                html = '';
                contador = 0;
                $.each(result, function (key, obj) {
                    contador++;
                    html += "<tr>";
                    html += "<td>" + contador + "</td>";
                    html += "<td>" + obj['nombre'] + "</td>";

                    if (obj['estado_permiso'] == 1) {
                        html += "<td><center><input type='checkbox' name='estado_permiso' value=1 checked></center></td>";
                    } else {
                        html += "<td><center><input type='checkbox' name='estado_permiso' value=0></center></td>";
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
            data: {usuario: usuario},
            type: 'post',
            dataType: 'json',
            success: function (result) {

                html = '';
                contador = 0;
                $.each(result, function (key, obj) {
                    contador++;
                    html += "<tr>";
                    html += "<td>" + contador + "</td>";
                    html += "<td>" + obj['nombre'] + "</td>";

                    if (obj['estado_permiso'] == 1) {
                        html += "<td><center><input type='checkbox' name='estado_permiso' value=1 checked></center></td>";
                    } else {
                        html += "<td><center><input type='checkbox' name='estado_permiso' value=0></center></td>";
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
       
       alert("Grabar!!!!!");
        
        
    });






})