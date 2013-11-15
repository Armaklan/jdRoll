var onLoadControllerImpl = function() {

	var generals = [];
	var campagnes = [];
	var withChats = [];

	function executeAllMethods(methods) {
		methods.forEach(function(method) {
			method();
		})
	}

	return {
		generals: generals,
		campagnes: campagnes,
		withChats: withChats,
		onLoadGenerals: function() {
			executeAllMethods(generals);
		},
		onLoadChat: function() {
			executeAllMethods(withChats);
		},
		onLoadCampagnes: function() {
			executeAllMethods(campagnes);
		}
	}

}

var onLoadController = onLoadControllerImpl()