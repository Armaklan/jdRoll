/**
 * Control Draft editor actions
 *
 * @package draft
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

var draftControllerImpl = function() {

    return {
        ajaxEnreg : function(campagne_id) {
            topic_id = $("input[name=topic_id]").val();
            perso_id = $("input[name=perso_id]").val();
            tinyMCE.triggerSave();
            content = $("#content").val();
            
            $.ajax({
                type: "POST",
                url: BASE_PATH + "/forum/" + campagne_id + "/topic/draft",
                data: {
                    topic_id: topic_id,
                    perso_id: perso_id,
                    content: content
                },
                success: function(msg){$("#enregResult").text(msg)},
                error: function(msg) {}
            });
        }
    }
}

var draftController = draftControllerImpl();