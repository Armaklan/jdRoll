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

    component.openModalPropertySheet = function() {
        getModalPropertySheet().modal('show');
    };
		
	//Fermeture de la fenêtre de propriétés
	component.closeModalPropertySheet = function(e) {	
		if($('#radWYSIWYG').prop("checked"))
		{
		
		}
		else
		{
			alert('switch image');
			$('#zoneImg').attr('src',$('#PropImgBG').val());
			$('#imgBG').attr('value',$('#PropImgBG').val());
			$('#typeFiche').attr('value',1);
		}
        getModalPropertySheet().modal('hide');
    };
	
	component.openModalTemplateSheet = function() {
		
		$('#templateSheetHelp').html(PROP_SHEET_OPT_1);
        getModalTemplateSheet().modal('show');
    };
	
	//Closing 
	component.closeModalTemplateSheet = function(campagne) {	
		
		var data=$('#templateSheetForm').serialize();
		alert(data);
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
	
    

	//Changement du background de la fiche
	component.switchBackgroundProperty = function() {

        if($("#radWYSWIG").prop("checked"))
		{
			$('#imgBG').css('display','none');
			$('#uploadImg').css('display','none');
			$('#propSheetHelp').html(PROP_SHEET_OPT_1);
		}
		else
		{
			$('#imgBG').css('display','');
			$('#uploadImg').css('display','');
			$('#propSheetHelp').html(PROP_SHEET_OPT_2);
		}
        
    };
	
	//Initialisation du composant
	component.Init = function(imageTemplate) {

       if(imageTemplate == '')
	   {
			$("#radWYSWIG").prop("checked", true);
			this.switchBackgroundProperty(); 
	   }
	   else
	   {
			$("#radImg").prop("checked", true)
			this.switchBackgroundProperty(); 
	   
	   }
        
    };
	
    return component;
})();
