app.directive('jdrollAuthform', function() {
    return {
      templateUrl: 'views/AuthForm.html',
	  link: function (scope, elem, attrs) {
	  
	    //On stoppe la propagation de l'événement de click pour éviter la fermeture intempestive de la fenêtre d'auth
		elem.bind('click', function (e) {
                e.stopPropagation();
            });
		}
	};
});
  
app.directive('jdrollAlert', function() {
    return {
        scope: {
            level: '@',
			message: '@'
        },
        templateUrl: 'views/alert.html'
    };
});
 
  