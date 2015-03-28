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
    $routeProvider
        .when('/carte/create', {
            templateUrl: 'js/angular/carte/index-creator.html',
            controller: 'CtrlCarteCreator'
        })
        .when('/carte/:carteId', {
            templateUrl: 'js/angular/carte/index-manager.html',
            controller: 'CtrlCarteManager',
            resolve: {
                carte: function($route, $http, $q, $rootScope){
                    //Create our own promise object
                    var deferred = $q.defer();
                    //First, fetch the carte information
                    $http.get('carte/' + $route.current.params.carteId).then(function(response){
                        var carte = response.data;
                        //We must know the dimensions of the image before launching everything
                        var request = $rootScope.getImageDimension(carte.image).then(function(dimensions){
                            carte.dimensions = dimensions;
                            deferred.resolve(carte);
                        });
                        $rootScope.loadingTracker.addPromise(request);
                    });
                    //Return the promise
                    return deferred.promise;
                }
            }
        })
    ;
}]).run(function($rootScope, $q, promiseTracker){
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
    };

    //Create promise tracker
    $rootScope.loadingTracker = promiseTracker();

    //Get campagne ID
    $rootScope.getCampagneId = function(){
        return parseInt(window.location.pathname.split('/').slice(-2)[0]);
    }
})
;