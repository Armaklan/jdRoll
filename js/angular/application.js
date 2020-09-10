/**
 * Created by Cadrach on 21/03/15.
 */
var ngApplication = angular.module('jdroll', [
    'ngRoute',
    'ngDragDrop',
    'leaflet-directive',
    'ajoslin.promise-tracker',//Promise Tracker
    'mgcrea.ngStrap', //UI directives
    'angular-growl', //Growl messages
    'jdRoll.forum'

]).config(['$locationProvider', '$routeProvider', '$httpProvider', 'growlProvider',
function($locationProvider, $routeProvider, $httpProvider, growlProvider) {

    $locationProvider.hashPrefix('');

    $routeProvider
        .when('/carte/create', {
            templateUrl: 'js/angular/carte/index-creator.html',
            controller: 'CtrlCarteCreator'
        })
        .when('/carte/list', {
            templateUrl: 'js/angular/carte/index-list.html',
            controller: 'CtrlCarteList',
            resolve: {
                cartes: function($rootScope, $http){
                    return $http.get('carte/list/' + $rootScope.getCampagneId()).then(function(response){
                        return response.data;
                    });
                }
            }
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

    //Register the interceptor via an anonymous factory
    //Using "unshift" to get at the head of the interceptors
    $httpProvider.interceptors.unshift(function($q, growl, $rootScope) {
        return {
            'request': function(config){
                config.tracker = $rootScope.loadingTracker;
                return config;
            },
            'responseError': function(rejection) {
                // do something on error
                if(rejection.data.error){
                    growl.error(rejection.data.error.message);
//                        alert('ERROR');
                }
                return $q.reject(rejection);
            }
        };
    });

    //Setup Growl defaults
    growlProvider.globalTimeToLive(5000);

    
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
