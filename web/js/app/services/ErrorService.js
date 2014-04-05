angular.module("jdRoll.service.errors", []).
service('Errors', function($timeout) {

    var service = {};

    service.list = {};

    service.add = function(msg, gravity) {
        var timestamp = (new Date()).getTime();
        service.list[timestamp] = {
            msg: msg,
            gravity: gravity || "danger"
        };
        $timeout(function () {
            delete service.list[timestamp];
        }, (8) * 1000);
    };

    return service;
});