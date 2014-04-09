/**
 * Created by zuberl on 04/04/2014.
 */

angular.module('jdRoll.controller.games.my', ['jdRoll.service.game', 'jdRoll.service.session']).
    controller('MyGamesController', function($rootScope, $scope, Game, SessionService){

        $rootScope.campaignSpace = false;

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
    });
