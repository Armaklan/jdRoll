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
          'authenticate': { method:'POST', isArray:false,params: {action: 'authentication'}},
		  'GetSession': { method:'GET', isArray:false,params: {action: 'getSession'}}
      });
}]);