var codigo = 0;
var sentencia_crud = '';
$(document).ready(function(){

     $("#btnOpen").click(function() {
         configModal(0, 'nuevo','Nuevo registro',null);
    });
        
    configModal = function(id, ope, titulo,usuario){

        controlador = 'mvc';
        if (usuario == "usuario") {//Viene por misdatos
            controlador = 'index';
        }
        codigo = id;
        sentencia_crud = ope;
        $.ajax({
            url: urls.siteUrl + '/admin/'+controlador+'/operacion/ajax/form',
            data:{id:id},
            type:'post',
            success: function(result) {
                
                $('#ventana-modal').empty().html(result);
                $(".v_numeric").numeric();
                $(".v_decimal").numeric(',');
                $(".v_datepicker").datepicker({
                    changeMonth: true,
                    changeYear: true
                    });
                    
                $("#padre").change(function(){
                    var padre = $("#padre").val();
                    
                    $.ajax({
                        url:urls.siteUrl + '/admin/recurso/num-recurso-correlativo',
                        data:{padre:padre},
                        type:'post',
                        dataType:'json',
                        success: function(result) {
                            $('#orden').val(result);
                        }
                        
                        
                    })
                    
               })
                   
                $('#ventana-modal').dialog({
                //height: 'auto',
                height:500,
                width: 620, //1050
                modal: true,
                //maxHeight: 400,
                resizable: false,
                title:titulo,
                buttons: {
                    "Guardar": function() {
                    dialog = $(this);
                    
                    $.ajax({
                    url: urls.siteUrl + '/admin/'+controlador+'/operacion/ajax/validar',
                    data: $('#form').serialize(),
                    type:'post',
                    success: function(result) {
                       if(validarCampos(result)){
                           $.ajax({
                               url: urls.siteUrl + '/admin/'+controlador+'/operacion/ajax/save/scrud/' + sentencia_crud + '/id/'+ codigo,
                               data: $("#form").serialize(),
                               success: function(result){
                                    location.reload();
                               }
                           });
                       }
                    }
                    })

                    },
                     "Cancelar": function() {
                       $(this).dialog("close");
                        
                    }
                },
                close: function() {//$("#ventana-modal").remove();
                }
                });
            }
        })     
    }
    
    nuevo = function() {
        configModal(0, 'nuevo','Nuevo registro');
    }
    
    editar = function(id){
        configModal(id, 'edit','Editar registro',null);
    }

    editarUsuario = function(id){
        configModal(id, 'edit','Editar registro','usuario');
    }
    
    
    elimina = function(id){
        
        codigo = id;
   
                $('#ventana-modal').empty().html('¿Está seguro que desea eliminar registro?');
                $('#ventana-modal').dialog({
                height: 'auto',
                width: 350, 
                modal: true,
                resizable: false,
                title:'Mensaje del sistema',
                buttons: {
                    "Eliminar": function() {
                    dialog = $(this);
                    $.ajax({
                        url: urls.siteUrl + '/admin/mvc/operacion/ajax/delete',
                        data:{id:codigo},
                        success: function(result){
                            location.reload();
                        }
                    });
                    },
                     "Cancelar": function() {
                       $(this).dialog("close"); 
                    }
                },
                close: function() {//$("#ventana-modal").remove();
                }
                });
         
    }
    
    verRecursos = function (id) {
        $.ajax({
            url: urls.siteUrl + '/admin/recurso/listado/ajax/listado/id_rol/' + id,
            type: 'post',
            dataType: 'json',
            success: function(result) {
                tablaRecurso(result,id);
            }
        })
    }
    
    seleccionaTodos = function() {
        alert('Falta programar');
		var checkedStatus = $("#title-table-checkbox").checked;
		var checkbox = $("#myModal").parents('.widget-box').find('tr td:first-child input:checkbox');		
		checkbox.each(function() {
			$("#title-table-checkbox").checked = checkedStatus;
			if (checkedStatus == $("#title-table-checkbox").checked) {
				$("#title-table-checkbox").closest('.checker > span').removeClass('checked');
			}
			if ($("#title-table-checkbox").checked) {
				$("#title-table-checkbox").closest('.checker > span').addClass('checked');
			}
		});
    };
    
    tablaRecurso = function(data,rol) {
        
        $('.modal-body').empty();
        html = '';
        html += '<div class="widget-box">';
        html += '<div class="widget-title">';	
        html += '<h5>Recursos</h5>';
        html += '</div>';
        html += '<div class="widget-content nopadding">';
        html += '<table id="tablaRecurso" class="table table-condensed table-bordered">';
        html += '<thead>';
        html += '<tr><th></th><th>Nombre</th><th>Descripción</th><th>Estado</th><th>Url</th></tr>';
        html += '</thead>';
        html += '<tbody>';
        
        $.each(data, function(key,obj) {
                    estado = 'checkmark.png';
                    html += '<tr>';
                    checked = ''
                    if (obj['checked']== 1)
                    checked = 'checked';
                    
                    html += '<td style="text-align:center"><input type="checkbox" name="check_recursos" '+checked+' value="'+obj['id']+'" /></td>';
                    html += '<td>' + obj['nombre'] + '</td>';
                    accion = obj['accion'];
                    if (obj['accion'] == '' || obj['accion'] == null) {
                        accion = '';
                    }
                    html += '<td>' + accion + '</td>';
                    
                    if (obj['estado'] == 0) {
                        estado = 'error.png';
                    }
                    
                    html += '<td width=8%><center><span style=display:none>';
                    html += obj['estado'] + '</span><img src='  + urls.siteUrl + '/img/' + estado + ' width=15%></center></td>';
                    url = obj['url'];
                    if (obj['url'] == '' || obj['url'] == null) {
                        url = '';
                    }
                    html += '<td>' + url + '</td>';
                    html += '</tr>';
 
        })
        
        html += '</tbody>';
        html += '</table>';
        html += '</div>';
        html += '</div>';
        
        $('#ventana-modal').empty().html(html);
        $('#tablaRecurso').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"sDom": '<""l>t<"F"fp>'
                
	});
        
        $('#ventana-modal').dialog({
                height: 'auto',
                width: 1000, 
                modal: true,
                resizable: false,
                title:'Lista de recursos',
                buttons: {
                    "Guardar": function() {
                    dialog = $(this);
                    
                    //Recorrer y guardar los recursos
                    //alert($('input[name="check_recursos"]:checked').val());
                    var selectedItems = new Array();
                    var recursosAdd = '';
                    var recursosDelete = '';
                    var pr = '';
		
                    $("input[@name='check_recursos[]']:checked").each(function(){
                            selectedItems.push($(this).val());
                            recursosAdd += $(this).val() + ',';
                    });
                    
                    $("input[@name='check_recursos[]']:not(:checked)").each(function(){
                        if ($(this).val() != '')
                            recursosDelete += $(this).val() + ',';
                    });
            
                    $.ajax({
                        url: urls.siteUrl + '/admin/recurso/agregar-recursos',
                        data:{
                            rec_add:selectedItems,
                            rec_del:recursosDelete,
                            rol:rol
                        },
                        type:'post',
                        success:function (result) {
                             location.reload();
                             //$(this).dialog("close");
                        }
                    })
                    
                    
                    
                    //alert(selectedItems);

                    },
                     "Cancelar": function() {
                       $(this).dialog("close");
                        
                    }
                },
                close: function() {//$("#ventana-modal").remove();
                }
                });
        
     
        
    }
    
    
 
  
})