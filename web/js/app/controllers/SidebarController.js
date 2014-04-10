/**
 * Created by zuberl on 04/04/2014.
 */

angular.module('jdRoll.controller.sidebar', ['jdRoll.service.game', 'jdRoll.service.session']).
    controller('SidebarController', function($rootScope, $scope, Game, SessionService){

        $scope.gamesLoading = true;
        $scope.tabset = [
            {
                label: "Parties Maitrisées",
                search: {
                    is_mj: "1",
                    is_active: "1"
                }
            },
            {
                label: "Parties Jouées",
                search: {
                    is_mj: "0",
                    is_favoris: "0",
                    is_active: "1"
                }
            },
            {
                label: "Parties Favorites",
                search: {
                    is_favoris: "1"
                }
            },
        ];

        $scope.$watch('authentInfo', function() {
            $scope.gamesLoading = true;

            $scope.games = Game.query({
                userId: SessionService.getAuthentInformation().user.id
            });

            $scope.games.$promise.then(function() {
                $scope.gamesLoading = false;
            });
        }, true);
        
    });
