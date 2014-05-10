(function(angular){

    var module = angular.module("jdRoll.directives.user", []);

    module.directive('jdrollAuthform', function() {
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

    module.directive('jdrollAlert', function() {
        return {
            scope: {
                level: '@',
                message: '@'
            },
            templateUrl: 'views/alert.html'
        };
    });

})(angular);
