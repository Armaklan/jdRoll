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
        return nowDate.getHours() +
                    ":" +
                    nowDate.getMinutes() +
                    ":" +
                    nowDate.getSeconds();
    }

    function refreshSaveTime() {
        $("#enregResult").html(
            'Enregistré à ' + getNowHour() + ' <i class="icon-save"></i>'
        );
    }

    function ajaxPost() {
        $('#waitingPost').removeClass('hide');
        $('#btn-reply').attr("disabled","disabled");
        var id = $("input[name=id]").val();
        var topicId = $("input[name=topic_id]").val();
        var persoId;
        if( $("input[name=perso_id]").length ) {
            persoId = $("input[name=perso_id]").val();
        } else {
            persoId = $('select[name=perso_id]').select2('val');
        }
        tinyMCE.triggerSave();
        content = $("#content").val();

        $.ajax({
            type: "POST",
            timeout:10000,
            url: BASE_PATH + "/forum/" + CAMPAGNE_ID + "/post/save",
            data: {
                id: id,
                topic_id: topicId,
                perso_id: persoId,
                content: content
            }}).
        done(function(msg){
            window.location.assign(BASE_PATH + msg);
        }).
        fail(function(msg) {
            $("#enregResult").html(
                '<span class="alert alert-danger">Impossible de poster le message <i class="icon-save"></i></span>'
            );
            $('#btn-reply').removeAttr("disabled");
        }).
        always(function() {
          $('#waitingPost').addClass('hide');
        });

    }

    function preview() {
        tinyMCE.triggerSave();
        var content = $("#content").val();
        $.ajax({
            type: "POST",
            url: BASE_PATH + "/forum/" + CAMPAGNE_ID + "/preview",
            data: {
                content: content
            },
            success: function(msg){
                $("#previewCell").html(msg.content);
                $("tr#previewRow").removeClass("hide");
            },
            error: function(msg) {}
        });
    }

    function ajaxEnreg() {
        topic_id = $("input[name=topic_id]").val();
        var perso_id;
        if( $("input[name=perso_id]").length ) {
            perso_id = $("input[name=perso_id]").val();
        } else {
            perso_id = $('select[name=perso_id]').select2('val');
        }
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
        autoSave: autoSave,
        ajaxPost: ajaxPost,
        preview: preview
    };
};

var draftController = draftControllerImpl();
onLoadController.generals.push(draftController.autoSave);
