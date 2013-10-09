/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



function ajaxEnreg(path, campagne_id) {
   
    topic_id = $("input[name=topic_id]").val();
    perso_id = $("input[name=perso_id]").val();
    tinyMCE.triggerSave();
    content = $("#content").val();
    
    $.ajax({
        type: "POST",
        url: path + "/forum/" + campagne_id + "/topic/draft",
        data: {
            topic_id: topic_id,
            perso_id: perso_id,
            content: content
        },
        success: function(msg){$("#enregResult").text(msg)},
        error: function(msg) {}
    });
}