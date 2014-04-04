var app = angular.module("jdRollApp", ['ngResource','ngRoute', 'jdRoll.service.session', 'jdRoll.service.user', 'jdRoll.controller.main']);

app.config(['$routeProvider', function($routeProvider) {
    $routeProvider.
		when('/',{templateUrl: 'views/main.html'}).
		when('/profil', {templateUrl: 'views/profile.html', Controller: 'ShowOrdersController', isSecured: false}).
		when('/forgetPassword', {templateUrl: 'views/resetPwd.html',Controller: 'ShowOrdersController', isSecured: false}).
		when('/resetPassword/:alea', {templateUrl: 'views/resetPwd.html',Controller: 'ShowOrdersController', isSecured: false}).
        otherwise({redirectTo: '/'});
}]);

app.run(function ($rootScope, $location, SessionService, User) {

  $rootScope.$on('$routeChangeStart', function (event, next, current) {

	var authentInformation = SessionService.getAuthentInformation();

	if (!authentInformation.hasCheck) {
		var user = User.current({userId:''}, function(){
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



