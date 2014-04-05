/**
 * Created by zuberl on 04/04/2014.
 */

angular.module('jdRoll.controller.games.my', ['jdRoll.service.game']).
    controller('MyGamesController', function($rootScope, $scope, Game){

        $rootScope.campaignSpace = false;

        $scope.tabset = [
            {
                label: "En-préparation",
                search: {
                    statut: "3"
                },
                active: false
            },
            {
                label: "En-Cours",
                search: {
                    statut: "0"
                },
                active: true
            },
            {
                label: "Archivée",
                search: {
                    statut: "2"
                },
                active: false
            }
        ];

        $scope.hasActivity = function (elt) {
            return elt.activity > 0;
        };

        $scope.games = Game.query({
            userId: 1
        });
    });
