(function() {
    "use strict";

    angular
        .module('jdRoll.sidebar.character', [])
        .service('CharacterService', CharacterService)
        .controller('SidebarCharacterController', SidebarCharacterController)
        .directive('jdCharacterLink', CharacterLink);

    function SidebarCharacterController($scope, CharacterService) {
        $scope.refreshCharacters = function(search) {
            CharacterService.get(search).then(function(data) {
                $scope.characters = data;
            });
        };

        $scope.openModalCharacter = function(character) {
            persoModalService.openPerso(character.campagneId, character.id);
        };
    }

    function CharacterService($http) {
        this.get = get;

        function get(text) {
            return $http({
                url : '/apiv2/character/' + window.CAMPAGNE_ID,
                params: {
                    text: text
                }
            }).then(function(ret) {
                return ret.data;
            });
        }
    }

    function CharacterLink() {
        return {
            restrict: 'A',
            templateUrl: 'js/angular/sidebar/character-link.html',
        }
    }


})();