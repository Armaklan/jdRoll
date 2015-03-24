/**
 * Created by Cadrach on 21/03/15.
 */
var ngApplication = angular.module('jdroll', [
    'ngRoute',
    'ngDragDrop',
    'leaflet-directive',
    'ajoslin.promise-tracker',//Promise Tracker
    'mgcrea.ngStrap' //UI directives

]).config(['$routeProvider',
function($routeProvider) {
    $routeProvider.
        when('/carte/:carteId', {
            templateUrl: 'js/angular/carte/index.html',
            controller: 'CtrlCarte',
            resolve: {
                carte: function($route, $http, $q, $rootScope){
                    //Create our own promise object
                    var deferred = $q.defer();
                    //First, fetch the carte information
                    $http.get('carte/' + $route.current.params.carteId).then(function(response){
                        var carte = response.data;
                        //We must know the dimensions of the image before launching everything
                        $rootScope.getImageDimension(carte.image).then(function(dimensions){
                            carte.dimensions = dimensions;
                            deferred.resolve(carte);
                        });
                    });
                    //Return the promise
                    return deferred.promise;
                }
            }
        });
}]).run(function($rootScope, $q){
    /**
     * Promise returning the dimension of an image
     * @param url
     * @returns {promise|Q.promise|fd.g.promise|qFactory.Deferred.promise}
     */
    $rootScope.getImageDimension = function(url){
        var deferred = $q.defer();
        $("<img/>").attr("src", url).load(function(){
            //Set the dimensions & resolve the promise
            deferred.resolve({w:this.width, h:this.height});
        });
        return deferred.promise;
    }
})
;