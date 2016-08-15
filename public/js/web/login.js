/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(function(){

    // Checking for CSS 3D transformation support
    $.support.css3d = supportsCSS3D();

    var formContainer = $('#loginbox');

    // Listening for clicks on the ribbon links
    $('.flip-link').click(function(e){

            // Flipping the forms
            formContainer.toggleClass('flipped');

            // If there is no CSS3 3D support, simply
            // hide the login form (exposing the recover one)
            if(!$.support.css3d){
                    $('#loginform').toggle();
            }
            e.preventDefault();
    });

    // A helper function that checks for the
    // support of the 3D CSS3 transformations.
    function supportsCSS3D() {
            var props = [
                    'perspectiveProperty', 'WebkitPerspective', 'MozPerspective'
            ], testDom = document.createElement('a');

            for(var i=0; i<props.length; i++){
                    if(props[i] in testDom.style){
                            return true;
                    }
            }

            return false;
    }
    
    $("#loginform").validate({
        rules:{
            usuario:{
                required:true,
                email: true
            },
            clave:{
                required: true,
                minlength:6,
                maxlength:20
            }
        },
        errorClass: "help-inline",
        errorElement: "span",
        highlight:function(element, errorClass, validClass) {
            $(element).parents('.control-group').removeClass('success');
            $(element).parents('.control-group').addClass('error');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).parents('.control-group').removeClass('error');
            $(element).parents('.control-group').addClass('success');
        },
        messages: {
            usuario: {
                required: "*"
            },
            clave: {
                required: "*"
            }
        }
    });
    
    $("#recoverform").validate({
        rules:{
            emailrecover:{
                required:true,
                email: true
            }
        },
        errorClass: "help-inline",
        errorElement: "span",
        highlight:function(element, errorClass, validClass) {
            $(element).parents('.control-group').removeClass('success');
            $(element).parents('.control-group').addClass('error');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).parents('.control-group').removeClass('error');
            $(element).parents('.control-group').addClass('success');
        },
        messages: {
            emailrecover: {
                required: "*"
            }
        }
    });
});
