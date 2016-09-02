var codigo = 0;
var sentencia_crud = '';
$(document).ready(function () {


    $("#n0").hide();
    $("#n0_chzn").hide();
    //$("#nivel").show();
    //$("#nivel_chzn").hide();

    $("#nivel").change(function () {

        if ($(this).val() == '') {
            $("#n0").hide();
        } else if ($(this).val() == 0) {
            //$("#n0").show();
            $("#n0_chzn").show();
        } else if ($(this).val() == 1) {
            $("#n0_chzn").show();
        } else if ($(this).val() == 2) {
            $("#n0_chzn").show();
        } else if ($(this).val() == 3) {
            $("#n0_chzn").show();
        } else if ($(this).val() == 4) {
            $("#n0_chzn").show();
        }


    });


});
