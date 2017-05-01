/**
 * Control Draft editor actions
 *
 * @package draft
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

var campagneControllerImpl = function() {

    function updateAdminOpen(state) {
        if(CAMPAGNE_ID !== 0) {
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


    function favorised(campagne, statut) {
        $.ajax({
            type: "POST",
            url: BASE_PATH + "/campagne/favoris",
            data: {campagne: campagne, statut: statut},
            success: function(msg){},
            error: function(msg) {}
        });
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
                    $('#admMode i').removeClass("icon-eye-close");
                    $('#admMode i').addClass("icon-eye-open");
                    updateAdminOpen(0);
                }
            });
        },
        onFavorised : function() {
            $('#favorised').click(function () {
                var campagneId = $(this).attr("data-campagne-id");
                if ( $("#favorised").hasClass("notFavorised") ) {
                    $("#favorised").removeClass("notFavorised");
                    $('#favorised').html("<i class='icon-eye-close'></i> Ne plus observer");
                    favorised(campagneId, 1);
                } else {
                    $("#favorised").addClass("notFavorised");
                    $('#favorised').html("<i class='icon-eye-open'></i> Observer la partie");
                    favorised(campagneId, 0);
                }
            });
        }
    };
};

var campagneController = campagneControllerImpl();
onLoadController.campagnes.push(campagneController.onAdminOpenChanged);
onLoadController.campagnes.push(campagneController.onFavorised);
