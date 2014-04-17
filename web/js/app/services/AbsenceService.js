angular.module("jdRoll.service.absence", []).
service('Absence', function($resource) {

   return $resource('api/absences/:id', {userId:'@id'}, {});

});