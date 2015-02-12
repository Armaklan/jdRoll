(function() {
  "use strict";

  angular
  .module('jdRoll.myGame', ['ui.bootstrap'])
  .controller('GamesController', gamesController)
  .directive('gamesBox', gamesBoxDirective)
  .service('Game', gameService);

  function gamesController(Game) {
    var self = this;

    self.tabset = [{
      label: "En-Cours",
      active: true,
      search: {
        statut: "0"
      }
    }, {
      label: "Favorites",
      active: false,
      search: {
        is_favoris: "1"
      }
    }, {
      label: "En-préparations",
      active: false,
      search: {
        statut: "3"
      }
    }, {
      label: "Archivées",
      active: false,
      search: {
        statut: "2"
      }
    }];

    this.hasActivity = function (elt) {
      return elt.activity > 0;
    };

    Game.all().then(function(data){
      self.games = data;
    });

  }

  function gameService($http) {
    this.all = all;

    function all() {
      return $http({url:BASE_PATH + '/api/game'}).then(function(ret) {
        return ret.data;
      });
    }
  }

  function gamesBoxDirective() {
    return {
      replace: true,
      scope: {
        gamesBox: '='
      },
      templateUrl: BASE_PATH + '/js/angular/my-game/game-box.html',
      controller: gameBoxController,
      controllerAs: "boxCtrl"
    };
  }

  function gameBoxController($scope) {
    this.urlToForm = getUrlToForm();

    function getUrlToForm() {
      return BASE_PATH + '/forum/' + $scope.gamesBox.id;
    }
  }

})();
