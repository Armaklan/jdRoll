angular.module("jdRoll.controller.main", ["jdRoll.service.session"]).
controller('MainController',function($rootScope, $scope,SessionService) {

    $rootScope.campaignSpace = false;
    $scope.authentInfo=SessionService.getAuthentInformation();

});