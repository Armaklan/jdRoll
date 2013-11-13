$(function(){
	
	$("#persoModal" ).css('display','none');
	$("#persoModal" ).dialog({
		height: 'auto',
		width: 'auto',
		autoOpen: false
	}).removeClass('ui-widget');
	
	$('#myPerso').click(function(e){
		
		if(!$("#persoModal").dialog("isOpen"))
			$("#persoModal").dialog("open");
		
		if($('.ui-dialog-titlebar').has('#SwitchFDP').length == 0)
		{
			if($('#zoneFichepopUp').has('#zoneFicheCustomPopUp').length > 0)
			{
				$('.ui-dialog-titlebar').append('<a href="#!" id="SaveFDP" class="btn iconeBtn sidebarBtn quickLink2 FDPToolBarBtn" title="Sauvegarder" role="button"><span><i class="icon-save"></i></span></a>');
				$('.ui-dialog-titlebar').append('<a href="#!" id="EditFDP" class="btn iconeBtn sidebarBtn quickLink2 FDPToolBarBtn" title="Editer la fiche" role="button"><span><i class="icon-edit"></i></span></a>');
			}
			$('.ui-dialog-titlebar').append('<a id="SwitchFDP" href="{{ path('perso_view_all', {campagne_id: campagne_id}) }}" class="btn iconeBtn sidebarBtn quickLink2 FDPToolBarBtn" title="Personnages" role="button"><span><i class="icon-user"></i></span></a>');
			
			if($('#zoneFichepopUp').has('#zoneFicheCustomPopUp').length > 0)
			{
				$('#EditFDP').click(function(e){
					if($('#SaveFDP').css('display') == 'none')
						$('#SaveFDP').css('display','block');
					else
						$('#SaveFDP').css('display','none');
					
					$('#zoneFichepopUp .editable').each(function(e) {
						
						if($(this).css('color') == 'rgb(0, 136, 204)')
							$(this).css('color','').editable('toggleDisabled');	
						else
							$(this).css('color','#0088cc').editable('toggleDisabled');
						
						if($(this).css('display') == 'none')
						{
							$(this).css('display','');
							$(this).addClass("editable-empty");
						}
						
						if($(this).hasClass("editable-empty"))
						{
							if($('#SaveFDP').css('display') == 'none')
								$(this).css('display','none');
							$(this).css('color','#DD1144');
						}	
					});
					
					$('#SaveFDP').click(function(e){
						var fieldsValue = '';
						$('#zoneFicheCustomPopUp').find('input[id$="hidden"]').each(function(){
							fieldsValue +=$(this)[0].outerHTML;
						});
						$.ajax({
							type: "POST",
							url: "{{ path('save_perso_popup', {"campagne_id" : campagne_id}) }}",
							data: {fields: fieldsValue},
							success: function(msg){},
							error: function(msg) {alert("Echec de sauvegarde de la fiche de personnage !");}
						});
					});
				});
}
}
});

});

$(document).ready(function () {
	$('#admMode').click(function () {
		if ( $("#admMode").hasClass("admDisabled") ) {
			$("#admMode").removeClass("admDisabled");
			$(".admIcone").removeClass("invisible");
			$('#admMode i').removeClass("icon-eye-open")
			$('#admMode i').addClass("icon-eye-close")
		} else {
			$("#admMode").addClass("admDisabled");
			$(".admIcone").addClass("invisible");
			$('#admMode i').removeClass("icon-eye-close")
			$('#admMode i').addClass("icon-eye-open")
		}
	});
});
$(document).ready(function () {
	$('#favorised').click(function () {
		if ( $("#favorised").hasClass("notFavorised") ) {
			$("#favorised").removeClass("notFavorised");
			$('#favorised i').removeClass("icon-star-empty");
			$('#favorised i').addClass("icon-star");
			favorised({{ campagne_id }}, 1);
		} else {
			$("#favorised").addClass("notFavorised");
			$('#favorised i').removeClass("icon-star");
			$('#favorised i').addClass("icon-star-empty");
			favorised({{ campagne_id }}, 0);
		}
	});
});

function favorised(campagne, statut) {
	$.ajax({
		type: "POST",
		url: "{{ path('favoris') }}",
		data: {campagne: campagne, statut: statut},
		success: function(msg){},
		error: function(msg) {}
	});
}
	