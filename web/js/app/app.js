var app = angular.module("jdRollApp", [
    'ngResource',
    'ngRoute',
    'jdRoll.service.session',
    'jdRoll.service.user',
    'jdRoll.service.errors',
    'jdRoll.controller.home',
    'jdRoll.controller.main',
    'jdRoll.controller.authentification',
    'jdRoll.controller.menu'
]);

app.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
        when('/', {
            templateUrl: 'views/main.html'
        }).
        when('/profil', {
            templateUrl: 'views/profile.html',
            Controller: 'ShowOrdersController',
            isSecured: false
        }).
        when('/forgetPassword', {
            templateUrl: 'views/resetPwd.html',
            Controller: 'ShowOrdersController',
            isSecured: false
        }).
        when('/resetPassword/:alea', {
            templateUrl: 'views/resetPwd.html',
            Controller: 'ShowOrdersController',
            isSecured: false
        }).
        otherwise({
            redirectTo: '/'
        });
    }
]);

app.run(function($rootScope, $location, SessionService, User) {

    $rootScope.$on('$routeChangeStart', function(event, next, current) {

        var authentInformation = SessionService.getAuthentInformation();

        if (!authentInformation.hasCheck) {
            var user = User.current({
                userId: ''
            }, function() {
                authentInformation.isLogged = true;
                authentInformation.username = user.username;
                authentInformation.isAdmin = (user.profil === 0);
            });
        }

        if (next.isSecured && !authentInformation.isLogged) {
            $location.path('/');
        }

    });

});


app.
config(function ($provide, $httpProvider) {
  
  $provide.factory('MyHttpInterceptor', function ($q, Errors) {
    return {

      // On request success
      request: function (config) {
        return config || $q.when(config);
      },
 
      // On request failure
      requestError: function (rejection) {
        Errors.add("Une erreur anormal s'est produite");
        return $q.reject(rejection);
      },
 
      // On response success
      response: function (response) {
        return response || $q.when(response);
      },
 
      // On response failture
      responseError: function (rejection) {
        Errors.add(rejection.data.error);    
        
        return $q.reject(rejection);
      }
    };
  });
 
  $httpProvider.interceptors.push('MyHttpInterceptor');
 
});

