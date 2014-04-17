/**
 * Created by zuberl on 04/04/2014.
 */

angular.module('jdRoll.controller.users', ['jdRoll.service.game', 'jdRoll.service.absence', 'jdRoll.service.user']).
controller('UserListController', function($location, $rootScope, $scope, Game, User){

    $rootScope.campaignSpace = false;
    $scope.usersLoading = true;

    $scope.users = User.query({});

    $scope.users.$promise.then(function(){
        $scope.usersLoading = false;
    });

    $scope.showUser = function(user) {
        $location.path("#/user/" + user.username);
    };
})
.controller('UserController', function($scope, $rootScope, $routeParams, Game, User, Absence){

    $rootScope.campaignSpace = false;
    $scope.userLoading = true;
    $scope.gamesLoading = true;

    $scope.tabset = [
        {
            label: "MJ sur",
            search: {
                statut: "0",
                is_mj: "1"
            }
        },
        {
            label: "PJ sur ",
            search: {
                statut: "0",
                is_mj: "0"
            }
        }
    ];


    $scope.user = User.get({userId: $routeParams.id});

    $scope.user.$promise.then(function(){
        $scope.userLoading = false;

        $scope.games = Game.query({
            userId: $scope.user.id
        });

        $scope.absences = Absence.query({
            userId: $scope.user.id
        });

        $scope.games.$promise.then(function() {
            $scope.gamesLoading = false;
        });

    });


});
