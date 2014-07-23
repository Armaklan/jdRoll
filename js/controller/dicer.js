/**
 * Control the dicer component
 *
 * @package notification
 * @copyright (C) 2014 jdRoll
 * @license MIT
 */
var uiDicerImpl = function() {

    var baseUrl = BASE_PATH + '/campagne/dicer/' + CAMPAGNE_ID + '/';

    function ajaxLaunchDice(topicId, param, description) {
        return $.ajax({
            type: 'POST',
            url: baseUrl + topicId,
            data: {param: param, description: description},
        });
    }

    function getNowDate() {
        var nowDate = new Date();
        return nowDate.getFullYear()
                    + '-' 
                    + nowDate.getMonth() + 1
                    + '-' 
                    + nowDate.getDate()
                    + ' '
                    + nowDate.getHours()
                    + ':' 
                    + nowDate.getMinutes()
                    + ':' 
                    + nowDate.getSeconds()
                    ;
    }

    function dicerLaunch(topicId) {
        param=$('#dicerParamQuick').val();
        description=$('#dicerDescriptionQuick').val();

        $('#waitingQuickLaunch').removeClass('hide');
        ajaxLaunchDice(topicId, param, description).
        done(function(retour){
            $('#resultatDicerQuick').html(retour);

            var text = 'Vous avez lancé ' + param + ' et obtenu : ' + retour + '<br>'
                + 'Description : ' + description;
                
            $('#quickDicerRow').before(
                '<tr>' + 
                '    <td></td>' +
                '    <td>' + text + '</td>' + 
                '</tr>'
            );
        }).
        fail(function() {
            $('#resultatDicerQuick').html('Une erreur s\'est produite.');
        }).
        always(function() {
            $('#waitingQuickLaunch').addClass('hide');
        });

        return false;
    }

    function dicerModalLaunch() {
        var param=$('#dicerParam').val();
        var description=$('#dicerDescription').val();
        $('#waitingLaunch').removeClass('hide');
        ajaxLaunchDice(0, param, description).
        then(function(retour){

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
        }).
        fail(function() {
            $('#resultatDicer').html('Une erreur s\'est produite.');
        }).
        always(function() {
            $('#waitingLaunch').addClass('hide');
        });;
        return false;
    }

    return {
        dicerModalLaunch : dicerModalLaunch,
        dicerLaunch: dicerLaunch
    };
};

var uiDicer = uiDicerImpl();