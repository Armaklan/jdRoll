/**
 * Control Draft editor actions
 *
 * @package draft
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

var draftControllerImpl = function() {

    function getNowHour() {
        var nowDate = new Date();
        return nowDate.getHours()
                    + ":" 
                    + nowDate.getMinutes()
                    + ":" 
                    + nowDate.getSeconds()
                    ;
    }

    function refreshSaveTime() {
        $("#enregResult").html(
            'Enregistré à ' + getNowHour() + ' <i class="icon-save"></i>'
        );
    }

    function ajaxEnreg() {
        topic_id = $("input[name=topic_id]").val();
        perso_id = $("input[name=perso_id]").val();
        tinyMCE.triggerSave();
        content = $("#content").val();

        $.ajax({
            type: "POST",
            url: BASE_PATH + "/forum/" + CAMPAGNE_ID + "/topic/draft",
            data: {
                topic_id: topic_id,
                perso_id: perso_id,
                content: content
            },
            success: function(msg){
                refreshSaveTime();
            },
            error: function(msg) {}
        });
    }

    function autoSave() {
        var element = $('#btn-enreg');
        if(element.length) {
            window.setInterval(ajaxEnreg, 60000);
        }
    }

    return {
        ajaxEnreg : ajaxEnreg,
        autoSave: autoSave
    }
}

var draftController = draftControllerImpl();
onLoadController.generals.push(draftController.autoSave);