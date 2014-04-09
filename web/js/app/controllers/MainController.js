angular.module("jdRoll.controller.main", ["jdRoll.service.session"]).
controller('MainController',function($rootScope, $scope, SessionService, Errors) {

    $rootScope.campaignSpace = false;
    $rootScope.title = 'jdRoll - Jeu de rôle par forum';
    $scope.authentInfo=SessionService.getAuthentInformation();
    $scope.errors = Errors.list;

});