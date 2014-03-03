/**
 * Control the dicer component
 *
 * @package notification
 * @copyright (C) 2014 jdRoll
 * @license MIT
 */
var uiDicerImpl = function() {

    var baseUrl = BASE_PATH + "/campagne/dicer/" + CAMPAGNE_ID + "/";

    function ajaxLaunchDice(topic_id, param, description, success) {
        $.ajax({
            type: "POST",
            url: baseUrl + topic_id,
            data: {param: param, description: description},
            error: function() { alert("Jet de dé impossible"); },
            success: function(retour){
                success(retour);
            }
        });
    }

    function getNowDate() {
        var nowDate = new Date();
        return nowDate.getFullYear()
                    + "-" 
                    + nowDate.getMonth() + 1
                    + "-" 
                    + nowDate.getDate()
                    + " "
                    + nowDate.getHours()
                    + ":" 
                    + nowDate.getMinutes()
                    + ":" 
                    + nowDate.getSeconds()
                    ;
    }

    function dicerLaunch(topic_id) {
        param=$('#dicerParamQuick').val();
        description=$('#dicerDescriptionQuick').val();
        ajaxLaunchDice(topic_id, param, description, function(retour){
            $('#resultatDicerQuick').html(retour);

            var text = 'Vous avez lancé ' + param + ' et obtenu : ' + retour + '<br>'
                + 'Description : ' + description;
                
            $('#quickDicerRow').before(
                '<tr>' + 
                '    <td></td>' +
                '    <td>' + text + '</td>' + 
                '</tr>'
            );
        });
        return false;
    }

    function dicerModalLaunch() {
        param=$('#dicerParam').val();
        description=$('#dicerDescription').val();
        ajaxLaunchDice(0, param, description, function(retour){

                $('#resultatDicer').html(retour);
                var strDate = getNowDate();

                $('#resultatDicerTable tbody tr:first').before(
                    '<tr>' + 
                    '    <td></td>' +
                    '    <td>' + strDate + '</td>' + 
                    '    <td>' + description + '</td>' +
                    '    <td>' + retour + '</td>' +
                    '</tr>'
                );
        });
        return false;
    }

    return {
        dicerModalLaunch : dicerModalLaunch,
        dicerLaunch: dicerLaunch
    };
};

var uiDicer = uiDicerImpl();
//onLoadController.campagnes.push(uiDicer.onLaunchDice);