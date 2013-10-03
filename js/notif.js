/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function toggleNotif() {
    if( $("#notif").hasClass("notifHide")) {
        $("#notif").removeClass("notifHide");
    } else {
        $("#notif").addClass("notifHide");
    }
}

function decrementeNbMsg() {
    var notifZone = $("#notifNumber");
    var nbMsg = notifZone.text().trim();
    nbMsg = nbMsg - 1;
    notifZone.text(nbMsg);
    if(nbMsg == 0) {
        setToNoMsg();
    }
}

function setToNoMsg() {
    $("#notifNumber").text("0");
    $("#notifIndicator").removeClass("btn-danger");
    $("#msg-zone").text("");
}

function ajaxDeleteNotif(path, idNotif) {
    $.ajax({
        type: "POST",
        url: path + "/notification/del",
            data: {id: idNotif},
        success: function(msg){},
        error: function(msg) {}
    });
}
        
function clearNotif(path) {
    $(".notifMsg").each(function(number, elt) {
        idNotif = $(this).attr("id").replace("notif", "");
        ajaxDeleteNotif(path, idNotif);
    });
    setToNoMsg();
}

function deleteNotif(path, idNotif) {
    ajaxDeleteNotif(path, idNotif);
    decrementeNbMsg();
}

