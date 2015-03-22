/**
 * Created by Cadrach on 21/03/15.
 */

var ngApplication = angular.module('jdroll', [
    'ngRoute',
    'ngDragDrop',
    'leaflet-directive',
    'ajoslin.promise-tracker'

]).config(['$routeProvider',
function($routeProvider) {
    $routeProvider.
        when('/carte/:carteId', {
            templateUrl: 'templates/carte/index.html',
            controller: 'CtrlCarte',
            resolve: {
                carte: function($route, $http, $q){
                    //Create our own promise object
                    var deferred = $q.defer();
                    //First, fetch the carte information
                    $http.get('carte/' + $route.current.params.carteId).then(function(response){
                        var carte = response.data;
                        //We must know the dimensions of the image before launching everything
                        $("<img/>").attr("src", carte.image).load(function(){
                            //Set the dimensions & resolve the promise
                            carte.dimensions = {w:this.width, h:this.height};
                            deferred.resolve(carte);
                        });
                    });
                    //Return the promise
                    return deferred.promise;
                }
            }
        });
}]);