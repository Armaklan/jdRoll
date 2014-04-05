angular.module("jdRoll.controller.main", ["jdRoll.service.session"]).
controller('MainController',function($rootScope, $scope, SessionService, Errors) {

    $rootScope.campaignSpace = false;
    $scope.authentInfo=SessionService.getAuthentInformation();
    $scope.errors = Errors.list;

});