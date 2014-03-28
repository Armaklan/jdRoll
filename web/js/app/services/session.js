//var app = angular.module("jdRollApp", []);

app.factory('SessionService', function() {

	var sdo = {
		isLogged: false,
		isAdmin: false,
		username: ''
	};
	return sdo;
});

app.factory('UserService', ['$resource', function($resource) {
  return $resource('/user/:id', null,
      {
          'authenticateUser': { method:'POST', isArray:false,params: {action: 'authentication'}},
		  'getUserSession': { method:'GET', isArray:false,params: {action: 'getUserSession'}},
		  'deleteUserSession': { method:'DELETE', isArray:false,params: {prop: 'session'}},
		  'resetUserPassword': { method:'POST', isArray:false,params: {action: 'resetPassword'}},
      });
}]);