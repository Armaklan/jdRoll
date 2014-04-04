angular.module("jdRoll.service.session", []).
service('SessionService', function($http) {
    var service = {};

    service.authentInformation = {
        isLogged: false,
        isAdmin: false,
        hasCheck: false,
        user: ''
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
            service.authentInformation.user = response.data;
            service.authentInformation.isAdmin = (response.data.profil == 2);
            return response.data;
        });
    };

    service.logout = function() {
        service.authentInformation.isLogged = false;
        service.authentInformation.user = {};
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
