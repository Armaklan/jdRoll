angular.module("jdRoll.controller.home", ['jdRoll.service.game', 'jdRoll.service.user']).
controller('HomeController',function($rootScope, $scope, Game, User) {

    $rootScope.campaignSpace = false;

    $scope.openGames = Game.query({
        enlistmentOpen: true
    });

    $scope.lastSubscribe = User.query({
        isLastSubscribe: true
    });

    $scope.recentConnected = User.query({
        isRecentConnected: true
    });

    $scope.missingUsers = User.query({
        isMissing: true
    });

    $scope.birthdayUsers = User.query({
        hasBirthday: true
    });


});