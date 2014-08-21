var EditorToolBarController = (function(){

    var component = {};
	
	/* Constant */
	
	var PROP_SHEET_OPT_1 = "En Choisissant cette option, vous créez votre fiche de personnage \
							a partir d'un fond construit à l'aide de <b>l'éditeur de texte</b> de JDRoll.<br\> \
							Cette solution convient parfaitement, aux joueurs désireurx d'avoir une  \
							fiche simple et fonctionnel, ainsi qu'au amateur de langage <b>HTML !</b>";
							
	var PROP_SHEET_OPT_2 = "En Choisissant cette option, vous créez votre fiche de personnage \
							a partir d'une <b>image</b> représenant la fiche des personnages ! <br\> \
							Vous pouvez utiliser une reproduction de la fiche d'origne, ou en créer une \
							et l'importer dans l'éditeur de fiche !<br\><br\> Une	fois l'image chargé dans \
							l'éditeur vous n'aurez plus qu'à définir des \
							zones d'édition, permettant aux joueurs de saisir les informations de  \
							leur personnages";

							
							
	/* Saving methods, duplicate from campagne-config.js, factorize ??? */
	
	var baseUrl = BASE_PATH + "/campagne/";

    var getUrl = function(campagne, type) {
        return baseUrl + campagne + "/sheet/" + type;
    };

    var save = function(type, campagne, data) {
        return $.ajax({
            type: "POST",
            url: getUrl(campagne, type),
            data: data
        });
	};						
   
	/* Opening and Closing modal dialog */
	
    var getModalPropertySheet = function() {
        return $("#propSheetPopup");
    };
	var getModalTemplateSheet = function() {
        return $("#templateSheetPopup");
    };
    var getModalVisuSheet = function() {
        return $("#visuSheetPopup");
    };

    //Opening property sheet popup
    component.openModalPropertySheet = function() {
        getModalPropertySheet().modal('show');
    };
		
	//Closing property sheet popup
	component.closeModalPropertySheet = function() {
		if($('#radWYSWIG').prop("checked"))
		{
            $('#zoneImg').css('display','none');
            $('#DivHTMLTemplate').css('display','');
            $('#typeFiche').attr('value',0);
            $('#bynEditionMode').css('display','');
		}
		else
		{
			$('#zoneImg').attr('src',$('#PropImgBG').val());
            $('#zoneImg').css('display','');
            $('#DivHTMLTemplate').css('display','none');
			$('#imgBG').attr('value',$('#PropImgBG').val());
			$('#typeFiche').attr('value',1);
            $('#bynEditionMode').css('display','none');
		}
        getModalPropertySheet().modal('hide');
    };

    //Opening template management popup
	component.openModalTemplateSheet = function() {
		
		$('#templateSheetHelp').html(PROP_SHEET_OPT_1);
        getModalTemplateSheet().modal('show');
    };
	
	//Closing template management popup
	component.closeModalTemplateSheet = function(campagne) {
		
		var data=$('#templateSheetForm').serialize();

		save('AddField',campagne,data).
        done(function() {
            setMsg("success", "Création du modèle !");
        }).
        fail(function(err) {
            setMsg("danger", err);
        });
       
		
        getModalTemplateSheet().modal('hide');
		 return false;
    };


    //Opening sheet visualization popup
    component.openModalVisuSheet = function() {

        var cloneDiv = $('#zoneFiche').clone();

        //Cleaning HTML
        cloneDiv.find('div[id^="JDRollUserControl"]').each(function(){
            $(this).removeClass("JDRollDroppedUserControl");
            $(this).resizable().resizable('destroy');
        });



        $('#DivVisuSheet').html(cloneDiv.html());

        //Enable reading mode
        $('#DivVisuSheet').find('a[id^="JDRollUserControlLink"]').each(function(){
            $(this).editable('toggleDisabled');
        });
        getModalVisuSheet().modal('show');
    };

    //Close visualization popup
    component.closeModalVisuSheet = function() {

        //Cleaning visualization area
        $('#DivVisuSheet').html('');

        getModalVisuSheet().modal('hide');
        return false;
    };
	

	//Change sheet background
	component.switchBackgroundProperty = function() {

        if($("#radWYSWIG").prop("checked"))
		{
			$('#PropImgBG').css('display','none');
			$('#uploadImg').css('display','none');
			$('#propSheetHelp').html(PROP_SHEET_OPT_1);

		}
		else
		{
			$('#PropImgBG').css('display','');
			$('#uploadImg').css('display','');
			$('#propSheetHelp').html(PROP_SHEET_OPT_2);
		}
        
    };
	
	//Initialization
	component.Init = function(imageTemplate) {

       if(imageTemplate == '')
       {
			$("#radWYSWIG").prop("checked", true);
            $('#bynEditionMode').css('display','');
       }
	   else
       {
			$("#radImg").prop("checked", true);
           $('#bynEditionMode').css('display','none');
       }

		this.switchBackgroundProperty();


        /* Initialize event callback */

        $('#bynEditionMode').click(function(e){

            if($('#zoneFicheCustomWYSIWIG').is(':hidden'))
            {
                $('#zoneFicheCustomWYSIWIG').css('display','block');
                $('#zoneFiche').css('display','none');
            }
            else
            {
                var fields = '';

                //get user control
                $('#zoneFiche').find('div[id^="JDRollUserControl_"]').each(function(){
                    fields += $(this)[0].outerHTML;
                });

                $('#DivHTMLTemplate').html(tinymce.get('templatev2').getContent());

                $('#zoneFicheCustomWYSIWIG').css('display','none');
                $('#zoneFiche').css('display','');

                //Re-enable rezisable functionnality
                $(".JDRollDroppedUserControl").draggable({
                    cursor: 'move',
                    containment: "parent"
                }).resizable().resizable('destroy');
                $(".JDRollDroppedUserControl").resizable();



            }
        });

        $('#BtnVisuEditMode').click(function(e){

            //Enable editable functionality
            $('#DivVisuSheet').find('a[id^="JDRollUserControlLink"]').each(function(){
                $(this).editable('toggleDisabled');
            });

            //Enable read mode
            $('#BtnVisuReadMode').css('display','');
            $(this).css('display','none');
        });

        $('#BtnVisuReadMode').click(function(e){

            //Disable editable functionality
            $('#DivVisuSheet').find('a[id^="JDRollUserControlLink"]').each(function(){
                $(this).editable('toggleDisabled');
            });

            //Enable edit mode
            $('#BtnVisuEditMode').css('display','');
            $(this).css('display','none');

        });

    };
	
    return component;
})();
