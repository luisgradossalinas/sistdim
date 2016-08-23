var codigo = 0;
var sentencia_crud = '';
$(document).ready(function(){
        
    configModal = function(id, ope, titulo,usuario, tipo){
        
        controlador = 'organigrama';
        
        codigo = id;
        sentencia_crud = ope;
        $.ajax({
            url: urls.siteUrl + '/admin/'+controlador+'/operacion/ajax/form/tipo/'+tipo,
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
                    url: urls.siteUrl + '/admin/'+controlador+'/operacion/ajax/validar/tipo/'+tipo,
                    data: $('#form').serialize(),
                    type:'post',
                    success: function(result) {
                       if(validarCampos(result)){
                           $.ajax({
                               url: urls.siteUrl + '/admin/'+controlador+'/operacion/ajax/save/scrud/' + sentencia_crud + '/id/'+ codigo + '/tipo/' + tipo,
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
    
    nuevoRegistro = function(tipo) {
        configModal(0, 'nuevo','Nuevo órgano',null,tipo);
    }
    
    editarRegistro = function(id,tipo){
        configModal(id, 'edit','Editar órgano',null,tipo);
    }
    
    grabarDatos = function(tipo) {
        
        
        alert($("#naturaleza_1").val() + "-" +$("#naturaleza_1 option:selected" ).text());
        alert($("#naturaleza_12").val() + "-" +$("#naturaleza_12 option:selected" ).text());
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
        
    }
    
})