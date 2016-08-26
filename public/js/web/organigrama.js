var codigo = 0;
var sentencia_crud = '';
$(document).ready(function () {

    //Ocultar el botón listar puestos;
    $("#buscar").hide();

    //Personalizar el listado de órganos
    $("#organo_chzn").css('width', '440px');
    $("#organo_chzn .chzn-drop").css('width', '430px');
    $("#organo_chzn .chzn-drop .chzn-search input").css('width', '380px');

    $("#unidad_chzn").css('width', '300px');
    $("#unidad_chzn .chzn-drop").css('width', '290px');
    $("#unidad_chzn .chzn-drop .chzn-search input").css('width', '240px');

    $("#unidad").change(function () {

        var unidad = $("#unidad").val();
        alert(unidad);
    });

    $("#buscar").click(function () {
        
        var organo = $("#organo").val();
        var unidad = $("#unidad").val();
        var nomunidad = $("#unidad option:selected").text();
        
        if (organo == '') {
            alert('Seleccione órgano');
            return false;
        }

        if (unidad == '') {
            alert('Seleccione unidad orgánica');
            return false;
        }

        alert(organo + " " +unidad + " " + nomunidad);
    });

    $("#organo").change(function () {

        var organo = $("#organo").val();
        if (organo == '') {
            return false;
        }
        //Realizar ajax para buscar unidades orgánicas
        $.ajax({
            url: urls.siteUrl + '/admin/organigrama/obtener-uorganica',
            data: {
                organo: organo
            },
            type: 'post',
            //Probar generando el html
            success: function (result) {

                $("#capa").html(result);
                $("#buscar").show();

                //location.reload();
                /*contador = 1;
                 $("#unidad").empty().append("<option value=''>[Seleccione unidad orgánica]</option>");
                 $.each(result, function(key,obj){
                 $("#unidad").append("<option value='"+obj['id_uorganica']+"'>"+obj['descripcion']+"</option>");
                 //$(".con-json select").append('<option value="'+id+'">'+value+'</option>');
                 html = "<li id='unidad_chzn_o_"+contador+"' class='active-result'>"+obj['descripcion'] +"</li>";
                 $("#unidad_chzn .chzn-drop .chzn-results").append(html);
                 contador++;
                 });*/

            }
        });



    })


    $('#tablaorgano').dataTable({
        "bJQueryUI": true,
        // searching: false,
        // paging: true,
        // scrollY: 400,
        "sPaginationType": "full_numbers"
                // "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>"
                //"sDom": '<""l>t<"F"fp>'
    });

    /*
     $('#tablaorgano').dataTable({
     "bJQueryUI": true,
     "sPaginationType": "full_numbers",
     // "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>"
     "sDom": '<""l>t<"F"fp>'
     });*/

    $('#tablaunidad').dataTable({
        "bJQueryUI": true,
        "sPaginationType": "full_numbers"
                // "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>"
                //"sDom": '<""l>t<"F"fp>'
    });

    configModal = function (id, ope, titulo, usuario, tipo) {

        controlador = 'organigrama';

        codigo = id;
        sentencia_crud = ope;
        $.ajax({
            url: urls.siteUrl + '/admin/' + controlador + '/operacion/ajax/form/tipo/' + tipo,
            data: {id: id},
            type: 'post',
            success: function (result) {

                $('#ventana-modal').empty().html(result);
                $(".v_numeric").numeric();
                $(".v_decimal").numeric(',');
                $(".v_datepicker").datepicker({
                    changeMonth: true,
                    changeYear: true
                });

                $('#ventana-modal').dialog({
                    height: 'auto',
                    width: 620,
                    modal: true,
                    resizable: false,
                    title: titulo,
                    buttons: {
                        "Guardar": function () {
                            dialog = $(this);
                            $.ajax({
                                url: urls.siteUrl + '/admin/' + controlador + '/operacion/ajax/validar/tipo/' + tipo,
                                data: $('#form').serialize(),
                                type: 'post',
                                success: function (result) {
                                    if (validarCampos(result)) {
                                        $.ajax({
                                            url: urls.siteUrl + '/admin/' + controlador + '/operacion/ajax/save/scrud/' + sentencia_crud + '/id/' + codigo + '/tipo/' + tipo,
                                            data: $("#form").serialize(),
                                            success: function (result) {
                                                location.reload();
                                            }
                                        });
                                    }
                                }
                            })

                        },
                        "Cancelar": function () {
                            $(this).dialog("close");

                        }
                    },
                    close: function () {//$("#ventana-modal").remove();
                    }
                });
            }
        })
    };

    nuevoRegistro = function (tipo) {
        /*var table = $('#tablaorgano').DataTable();
         table.search('Despacho');
         table.draw();*/
        configModal(0, 'nuevo', 'Nuevo registro', null, tipo);
    };

    editarRegistro = function (id, tipo) {
        configModal(id, 'edit', 'Editar registro', null, tipo);
    };

    //Actualizar las tablas de los órganos y las unidades orgánicas
    grabarDatos = function (tipo) {

        var data = new Array();
        var validar = '';
        $("#tabla" + tipo + " tbody tr").each(function () {
            var id = $(this).attr("data-" + tipo);
            var descripcion = $(this).find("td input").eq(1).val();
            var idp = $(this).find("td select").eq(0).val();
            validar = $(this).find("td").eq(0).text();
            data.push(id + "|" + descripcion + "|" + idp);
        });

        if (validar == 'No hay registros' || validar == 'No hay datos en la tabla') {
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
})