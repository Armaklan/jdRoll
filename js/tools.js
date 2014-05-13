function filterTable(idFilter, idTable) {
	searchText = $(idFilter).val().toLowerCase();
	$(idTable + " tr ").each(function(i){
		currentSearchIndex = $(this).find('.filterIndex').val().toLowerCase();
		if(currentSearchIndex != "header") {
			if( currentSearchIndex.indexOf(searchText) > -1) {
				$(this).css("display", "");
        $(this).removeClass("hide");
			} else {
				$(this).css("display", "none");
        $(this).addClass("hiding");
			}
		}
	});
}


function filterList(idFilter, idTable) {
	searchText = $(idFilter).val().toLowerCase();
	$(idTable + " > div ").each(function(i){
		currentSearchIndex = $(this).find('.filterIndex').val().toLowerCase();
		if(currentSearchIndex != "header") {
			if( currentSearchIndex.indexOf(searchText) > -1) {
				$(this).css("display", "");
        $(this).removeClass("hide");
			} else {
				$(this).css("display", "none");
        $(this).addClass("hiding");
			}
		}
	});
}

function onBtnDangerClick(form) {
    bootbox.confirm("L'action demandé est une action dangereuse (Suppression, Quitter une partie, ...). Etes-vous sur ? ", function(confirmed) {
      if(confirmed) {
          $(form).submit();
      }
  });
}

function selectAll(formName) {
    var checked = $(formName).find(".checkAll").is(':checked');
    $(formName).find('tr:not(.hiding)').find('input[type=checkbox]').prop('checked', checked);
}


$(function () {
	$(".collapserLink").click(function(e) {
       e.preventDefault();
       var sign = $(this).children('i');
       if(sign.hasClass('icon-chevron-sign-down')) {
        sign.removeClass("icon-chevron-sign-down").addClass("icon-chevron-sign-up");
    } else {
        sign.removeClass("icon-chevron-sign-up").addClass("icon-chevron-sign-down");
    }
});
});



$(function () {
	$("a.btn-danger").click(function(e) {
       e.stopPropagation();
       e.preventDefault();
       var location = $(this).attr('href');
       bootbox.confirm("L'action demandé est une action dangereuse (Suppression, Quitter une partie, ...). Etes-vous sur ? ", function(confirmed) {
           if(confirmed) {
               window.location.replace(location);
           }
       });
   });
});

function alarm(joueur, campagne) {
    var statutAlarm = 0;
    if ($(".alarm-pj").hasClass("alarm-on") ) {
        $(".alarm-pj").removeClass("alarm-on");
        var statutAlarm = 0;
    } else {
        $(".alarm-pj").addClass("alarm-on");
        var statutAlarm = 1;
    }

    $.ajax({
        type: "POST",
        url: "/campagne/alarm",
        data: {joueur: joueur, campagne: campagne, statut: statutAlarm},
        success: function(msg){},
        error: function(msg) {}
    });
}

