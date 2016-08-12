$(document).ready(function(){



	$("#mostrarMenu").click(function(){
	    var usuario = $("#usuario").val();
	    var proyecto = $("#proyecto").val();
	    
	    if (usuario == '') {
	    	alert("Seleccione un usuario");
	    	return;
	    }

	    if (proyecto == '') {
	    	alert("Seleccione un proyecto");
	    	return;
	    }

	    alert("ok");


/*
	    $.ajax({
	        url:urls.siteUrl + '/admin/proyecto/listado-proyectos',
	        data:{usuario:usuario},
	        type:'post',
	        dataType:'json',
	        success: function(result) {
	           // $('#orden').val(result);
	        }
	        
	        
	    })
	    */
	})


	
                   


})