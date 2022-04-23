/**
 * Control action execute "onLoadEnd" of page.
 *
 * @package notification
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

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

window.onLoadController = onLoadControllerImpl()