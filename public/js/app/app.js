angular.module("jdRollApp", ['ngRoute'])
	.config(['$routeProvider', function($routeProvider) {
	    $routeProvider.
	    	when('/login', {templateUrl: 'views/login.html'}).
	        otherwise({redirectTo: '/dashboard'});
	}]);
