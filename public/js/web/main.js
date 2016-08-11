$(function() {
    
    var jsBoot = function(){
        //Mensaje Flash Messenger
        this.flashMsg = function(){
            var mensajes = $('.alert'),
            h = 0,
            s = 'middle',
            interval = '1000';
            $.each(mensajes, function(k, v){
                h = 1000 * (k);
                setTimeout(function(){
                    $(v).fadeIn(s, h, function(){
                        setTimeout(function(){
                            if(!$(v).hasClass('showme')){
                                $(v).fadeOut(s);
                            }
                        }, h + interval);
                    });
                },h);
            });
        };
    };
    
    var ini = new jsBoot();
    ini.flashMsg();
});