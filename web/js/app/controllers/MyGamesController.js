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
                }
            },
            {
                label: "En-Cours",
                search: {
                    statut: "0"
                }
            },
            {
                label: "Archivée",
                search: {
                    statut: "2"
                }
            }
        ];

        $scope.hasActivity = function (elt) {
            return elt.activity > 0;
        };

        $scope.games = Game.query({
            userId: 1
        });
    });
