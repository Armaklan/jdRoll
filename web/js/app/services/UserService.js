angular.module("jdRoll.service.user", []).
service('User', function($resource) {


   return $resource('api/user/:userId', {userId:'@id'}, {
       current: {method: 'GET', url:'api/user/current' }
   });

});