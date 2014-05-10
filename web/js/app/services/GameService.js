angular.module("jdRoll.service.game", []).
service('Game', function($resource) {


   return $resource('api/games/:id', {id:'@id'}, {
   });

});
