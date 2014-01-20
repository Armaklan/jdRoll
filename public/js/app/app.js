var app = angular.module("jdRollApp", ['ngResource','ngRoute']);

	app.config(['$routeProvider', function($routeProvider) {
	    $routeProvider.
			when('/',{templateUrl: 'views/main.html'}).
			when('/profil', {templateUrl: 'views/profile.html',Controller: 'ShowOrdersController'}).
			when('/forgetPassword', {templateUrl: 'views/resetPwd.html',Controller: 'ShowOrdersController'}).
	        otherwise({redirectTo: '/'});
	}]);

app.run(function ($rootScope, $location, SessionService,UserService) {

 
  var routesThatRequireAuth = ['/user'];

  $rootScope.$on('$routeChangeStart', function (event, next, current) {

	UserService.getUserSession(null,function(data){
			
				SessionService.isLogged = true;
				SessionService.username = data.username;
				SessionService.isAdmin = data.profil == 0 ? true : false;
				
			},function(){
			
				SessionService.isLogged = false;
				SessionService.isAdmin = false;
				SessionService.username = '';
			});
  
	if(!SessionService.isLogged && $location.path() == "/profil")
		$location.path('/');
	
  });
});



