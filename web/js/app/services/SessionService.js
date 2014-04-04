angular.module("jdRoll.service.session", []).
service('SessionService', function($http) {
    var service = {};

    service.authentInformation = {
        isLogged: false,
        isAdmin: false,
        hasCheck: false,
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
            service.authentInformation.isLogged = true;
            service.authentInformation.username = response.data.username;
            service.authentInformation.isAdmin = response.data.profil === 0;
            return response.data;
        });
    };

    service.logout = function() {
        service.authentInformation.isLogged = false;
        service.authentInformation.username = "";
        service.authentInformation.isAdmin = false;
        $http({
            method: 'GET',
            url: 'api/logout'
        });
    };

    service.getAuthentInformation = function() {
        return service.authentInformation;
    };

	return service;
});
