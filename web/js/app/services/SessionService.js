angular.module("jdRoll.service.session", []).
service('SessionService', function($http) {
    var service = {};

	var authentInformation = {
		isLogged: false,
		isAdmin: false,
		username: ''
	};

    service.login = function(username, password) {
        return $http({
            method: 'POST',
            url: 'api/login',
            data: {
                username: username,
                password: password
            }
        }).then(function(response) {
            authentInformation.isLogged = true;
            return response.data;
        });
    };

    service.logout = function() {
        authentInformation.isLogged = false;
        authentInformation.username = "";
        authentInformation.isAdmin = false;
    };

    service.getAuthentInformation = function() {
        return authentInformation;
    };

	return service;
});
