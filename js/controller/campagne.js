/**
 * Control Draft editor actions
 *
 * @package draft
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

var campagneControllerImpl = function() {

    function updateAdminOpen(state) {
        if(CAMPAGNE_ID != 0) {
            $.ajax({
                type: "POST",
                url: BASE_PATH + "/campagne/admin_open",
                data: {
                    id: CAMPAGNE_ID,       
                    state: state
                },
                success: function(msg){},
                error: function(msg) {}
            });
        }
    }

    return {
        onAdminOpenChanged : function() {
            $('#admMode').click(function () {
                if ( $("#admMode").hasClass("admDisabled") ) {
                    $("#admMode").removeClass("admDisabled");
                    $(".admIcone").removeClass("invisible");
                    $('#admMode i').removeClass("icon-eye-open");
                    $('#admMode i').addClass("icon-eye-close");
                    updateAdminOpen(1);
                } else {
                    $("#admMode").addClass("admDisabled");
                    $(".admIcone").addClass("invisible");
                    $('#admMode i').removeClass("icon-eye-close")
                    $('#admMode i').addClass("icon-eye-open")
                    updateAdminOpen(0);
                }
            });
        }
    }
}

var campagneController = campagneControllerImpl();
onLoadController.campagnes.push(campagneController.onAdminOpenChanged);