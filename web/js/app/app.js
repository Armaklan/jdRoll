var app = angular.module("jdRollApp", [
    'ngResource',
    'ngRoute',
    'ui',
    'ui.bootstrap',
    'jdRoll.service.session',
    'jdRoll.service.user',
    'jdRoll.service.errors',
    'jdRoll.controller.home',
    'jdRoll.controller.main',
    'jdRoll.controller.sidebar',
    'jdRoll.controller.games.my',
    'jdRoll.controller.users',
    'jdRoll.controller.authentification',
    'jdRoll.controller.menu'
]);

app.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
        when('/404', {
            templateUrl: 'views/404.html'
        }).
        when('/', {
            templateUrl: 'views/main.html',
            controller: 'HomeController'
        }).
        when('/users', {
            templateUrl: 'views/users.html',
            controller: 'UserController'
        }).
        when('/games/my', {
            templateUrl: 'views/my_games.html',
            controller: 'MyGamesController',
            isSecured: true
        }).
        otherwise({
            redirectTo: '/404'
        });
    }
]);

app.run(function($rootScope, $location, $route, SessionService, User, Errors) {

    $rootScope.$on('$routeChangeStart', function(event, next, current) {

        var authentInformation = SessionService.getAuthentInformation();

        if (!authentInformation.hasCheck) {
            var user = User.current({}, function() {
                authentInformation.hasCheck = true;
                if(user.username) {
                    authentInformation.isLogged = true;
                    authentInformation.user = user;
                    authentInformation.isAdmin = (user.profil == 2);
                    $route.reload();
                } else {
                    if(next.isSecured) {
                        $location.path('/');
                        Errors.add("Une authentification est nécessaire");
                    }
                }
            }, function() {
                authentInformation.hasCheck = true;
                $location.path('/');
            });
        }

        if (authentInformation.hasCheck && next.isSecured && !authentInformation.isLogged) {
            Errors.add("Une authentification est nécessaire");
            $location.path('/');
        }

    });

});


app.config(function($provide, $httpProvider) {

    $provide.factory('MyHttpInterceptor', function($q, Errors) {
        return {

            // On request success
            request: function(config) {
                return config || $q.when(config);
            },

            // On request failure
            requestError: function(rejection) {
                Errors.add("Une erreur anormal s'est produite");
                return $q.reject(rejection);
            },

            // On response success
            response: function(response) {
                return response || $q.when(response);
            },

            // On response failture
            responseError: function(rejection) {
                Errors.add(rejection.data.error);

                return $q.reject(rejection);
            }
        };
    });

    $httpProvider.interceptors.push('MyHttpInterceptor');

});

angular.module('exceptionOverride', ['jdRoll.service.errors']).
factory('$exceptionHandler', function(Errors) {
    return function(exception, cause) {
        Errors.add("Une erreur s'est produite : " + exception.message);
    };
});