angular.module("jdRoll.service.game", []).
service('Game', function($resource) {


   return $resource('api/games/:userId', {userId:'@id'}, {
   });

});