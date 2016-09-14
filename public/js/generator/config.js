$(document).ready(function(){
 
 $("#id_tabla_chzn_o_0").click(function(){
   //  alert(1);
     
 })
 
 generarCrud = function(){
 
    //alert('Se generarán los CRUD .....');
    var valorSeleccionado = $('#id_tabla_chzn .chzn-single span').text();
    
    if (valorSeleccionado == "Seleccione")
    {
        alert("Debe seleccionar una opción!")
        return;
    }

    //Ejecutar AJAX para generar archivos según la opción seleccionada.
    $.ajax({
        url: urls.siteUrl + "/admin/generator/generar-code",
        //dataType: 'json',
        type: 'post',
        data:{'tabla':valorSeleccionado},
        success: function(data){
            
            $('#logGenerator').empty().html(data);
        }

    })

 }

});