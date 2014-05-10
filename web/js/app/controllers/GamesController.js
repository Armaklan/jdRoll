/**
 * Created by zuberl on 04/04/2014.
 */

(function(angular){

    var module = angular.module('jdRoll.controller.games', ['jdRoll.service.game', 'jdRoll.service.session']);

    module.controller('MyGamesController', function($rootScope, $scope, Game, SessionService){

        $rootScope.campaignSpace = false;
        $scope.gamesLoading = true;


        $scope.tabset = [
            {
                label: "En-Cours",
                search: {
                    statut: "0"
                }
            },
            {
                label: "Favorites",
                search: {
                    is_favoris: "1"
                }
            },
            {
                label: "En-préparations",
                search: {
                    statut: "3"
                }
            },
            {
                label: "Archivées",
                search: {
                    statut: "2"
                }
            }
        ];

        $scope.hasActivity = function (elt) {
            return elt.activity > 0;
        };

        $scope.games = Game.query({
            userId: SessionService.getAuthentInformation().user.id
        });

        $scope.games.$promise.then(function() {
            $scope.gamesLoading = false;
        });
    }).
    controller('EnlistGamesController', function($rootScope, $scope, Game, SessionService){

        $rootScope.campaignSpace = false;
        $scope.gamesLoading = true;
        $scope.pageTitle="Partie en cours de recrutement";

        $scope.games = Game.query({
            enlistmentOpen: true
        });

        $scope.games.$promise.then(function() {
            $scope.gamesLoading = false;
        });
    }).
    controller('OpenGamesController', function($rootScope, $scope, $modal, Game, SessionService){

        $rootScope.campaignSpace = false;
        $scope.gamesLoading = true;
        $scope.pageTitle="Partie en cours";

        $scope.games = Game.query({
            statut: '0'
        });

        $scope.games.$promise.then(function() {
            $scope.gamesLoading = false;
        });

        $scope.open = function(game) {
            var modalInstance = $modal.open({
                templateUrl: 'views/include/game-modal.html',
                controller: ModalGameInstController,
                resolve: {
                    game: function () {
                        return game;
                    }
                }
            });
        };
    }).
    controller('ArchiveGameController', function($rootScope, $scope, Game, SessionService){

        $rootScope.campaignSpace = false;
        $scope.gamesLoading = true;
        $scope.pageTitle="Partie archivée";

        $scope.games = Game.query({
            statut: '2'
        });

        $scope.games.$promise.then(function() {
            $scope.gamesLoading = false;
        });
    }).
    controller('PrepaGamesController', function($rootScope, $scope, Game, SessionService){

        $rootScope.campaignSpace = false;
        $scope.gamesLoading = true;
        $scope.pageTitle="Partie en préparation";

        $scope.games = Game.query({
            statut: '3'
        });

        $scope.games.$promise.then(function() {
            $scope.gamesLoading = false;
        });
    });


    var ModalGameInstController = function ($scope, $modalInstance, game) {

        $scope.game = game;

        $scope.ok = function () {
            $modalInstance.close();
        };

        $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
        };

    };


    module.controller('EnlistGamesController', function($rootScope, $scope, Game, SessionService){

        $rootScope.campaignSpace = false;
        $scope.gamesLoading = true;
        $scope.pageTitle="Partie en cours de recrutement";

        $scope.games = Game.query({
            enlistmentOpen: true
        });

        $scope.games.$promise.then(function() {
            $scope.gamesLoading = false;
        });
    });

    module.controller('OpenGamesController', function($rootScope, $scope, Game, SessionService){

        $rootScope.campaignSpace = false;
        $scope.gamesLoading = true;
        $scope.pageTitle="Partie en cours";

        $scope.games = Game.query({
            statut: '0'
        });

        $scope.games.$promise.then(function() {
            $scope.gamesLoading = false;
        });
    });

    module.controller('ArchiveGamesController', function($rootScope, $scope, Game, SessionService){

        $rootScope.campaignSpace = false;
        $scope.gamesLoading = true;
        $scope.pageTitle="Partie archivée";

        $scope.games = Game.query({
            statut: '2'
        });

        $scope.games.$promise.then(function() {
            $scope.gamesLoading = false;
        });
    });

    module.controller('PrepaGamesController', function($rootScope, $scope, Game, SessionService){

        $rootScope.campaignSpace = false;
        $scope.gamesLoading = true;
        $scope.pageTitle="Partie en préparation";

        $scope.games = Game.query({
            statut: '3'
        });

        $scope.games.$promise.then(function() {
            $scope.gamesLoading = false;
        });
    });

})(angular);
