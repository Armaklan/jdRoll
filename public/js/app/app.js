var app = angular.module("jdRollApp", ['ngResource','ngRoute']);

	app.config(['$routeProvider', function($routeProvider) {
	    $routeProvider.
	    	when('/login', {templateUrl: 'views/login.html',controller: 'AddOrdersController'}).
			when('/profile', {templateUrl: 'views/profile.html',Controller: 'ShowOrdersController'}).
	        otherwise({redirectTo: '/'});
	}]);

app.run(function ($rootScope, $location, SessionService,UserService) {

  // enumerate routes that don't need authentication
  var routesThatDontRequireAuth = ['/user'];

  $rootScope.$on('$routeChangeStart', function (event, next, current) {

	if(SessionService.username == '')
	{
		UserService.getUserSession(null,function(data){
		
			SessionService.isLogged = true;
			SessionService.username = data.username;
			SessionService.isAdmin = data.profil == 0 ? true : false;
			
		},function(){
		
			SessionService.isLogged = false;
			SessionService.isAdmin = false;
			SessionService.username = '';
		
		});
	}
	else
	{
    if (!SessionService.isLogged) {
     
      $location.path('/');
    }
	 }
  });
});



