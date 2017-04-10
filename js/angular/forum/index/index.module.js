(function() {
    "use strict";

    angular.module('jdRoll.forum.index', [
        'jdRoll.forum.index.controller'
    ]).config(routingConfig);

    function routingConfig($routeProvider) {
        $routeProvider
            .when('/forum/', {
                templateUrl: 'js/angular/forum/index/index.html',
                controller: 'ForumIndexController',
                controllerAs: 'indexCtrl'
            });
    }

})();
