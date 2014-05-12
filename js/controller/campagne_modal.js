/**
 * Control character sheet in modal
 *
 * @package notification
 * @copyright (C) 2014 jdRoll
 * @license MIT
 */
var uiCharacterModalControllerImpl = function() {

    function openModal() {
        $("#persoModal").modal("show");
    }

    function addButtonBar() {
        $('#persoModal .btn-bar').html('');
        /*
        FIXIT - Supprimer le temps de refaire correctement
        if($('#zoneFichepopUp').has('#zoneFicheCustomPopUp').length > 0)
        {
            $('#persoModal .btn-bar').append('<a href="#" id="SaveFDP" class="btn iconeBtn sidebarBtn quickLink2 FDPToolBarBtn" title="Sauvegarder" role="button"><span><i class="icon-save"></i></span></a>');
            $('#persoModal .btn-bar').append('<a href="#" id="EditFDP" class="btn iconeBtn sidebarBtn quickLink2 FDPToolBarBtn" title="Editer la fiche" role="button"><span><i class="icon-edit"></i></span></a>');
        }
        */
        var url = BASE_PATH + "/perso/view_all/" + CAMPAGNE_ID;
        $('#persoModal .btn-bar').append('<a id="SwitchFDP" href="' + url + '" class="btn iconeBtn sidebarBtn quickLink2 FDPToolBarBtn" title="Personnages" role="button"><span><i class="icon-user"></i></span></a>');

    }

    function onEditFdp() {
        $('#EditFDP').click(function(e){
            if($('#SaveFDP').css('display') == 'none') {
                $('#SaveFDP').css('display','block');
            } else {
                $('#SaveFDP').css('display','none');
            }

            $('#zoneFichepopUp .editable').each(function(e) {

                if($(this).css('color') == 'rgb(0, 136, 204)') {
                    $(this).css('color','').editable('toggleDisabled');
                } else {
                    $(this).css('color','#0088cc').editable('toggleDisabled');
                }

                if($(this).css('display') == 'none') {
                    $(this).css('display','');
                    $(this).addClass("editable-empty");
                }

                if($(this).hasClass("editable-empty")) {
                    if($('#SaveFDP').css('display') == 'none')
                        $(this).css('display','none');
                    $(this).css('color','#DD1144');
                }
            });
        });
    }

    function onSaveFdp() {
        $('#SaveFDP').click(function(e){
            var fieldsValue = '';
            $('#zoneFicheCustomPopUp').find('input[id$="hidden"]').each(function(){
                fieldsValue +=$(this)[0].outerHTML;
            });
            $.ajax({
                type: "POST",
                url: BASE_PATH + "/campagne/persoModal/save/popup/" + CAMPAGNE_ID,
                data: {fields: fieldsValue},
                success: function(msg){},
                error: function(msg) {alert("Echec de sauvegarde de la fiche de personnage !");}
            });
        });
    }

    function showModal() {
        if($('.ui-dialog-titlebar').has('#SwitchFDP').length === 0) {
            addButtonBar();
            openModal() ;
        }
    }

    return {
        prepareModal : function() {
            onEditFdp();
            onSaveFdp();
        },
        onPersoBtnClick: function() {
            $('#myPerso').click(function(e){
                showModal();
            });
        }
    };
};

var uiCharacterModalController = uiCharacterModalControllerImpl();
onLoadController.campagnes.push(uiCharacterModalController.prepareModal);
onLoadController.campagnes.push(uiCharacterModalController.onPersoBtnClick);
