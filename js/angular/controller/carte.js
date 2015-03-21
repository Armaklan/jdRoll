/**
 * TODO / To check&test
 * https://github.com/SINTEF-9012/Leaflet.MapPaint
 * https://github.com/ablakey/Leaflet.SimpleGraticule <== GRID
 */
ngApplication.controller('CtrlCarte', ['$scope', '$http', '$timeout', 'leafletData', 'leafletBoundsHelpers', 'carte',
    function ($scope, $http, $timeout, leafletData, leafletBoundsHelpers, carte) {

    /**
     * **************************
     * LOCAL VARIABLES
     * **************************
     */
    var bounds = [
        [-carte.dimensions.h/2, -carte.dimensions.w/2],
        [carte.dimensions.h/2, carte.dimensions.w/2]
    ];

    /**
     * **************************
     * SCOPE VARIABLES
     * **************************
     */
    $scope.carte = carte;
    $scope.options = {
        defaults: {
//            minZoom: 1,
            maxZoom: 4,
//            zoom: 1,
            crs: L.CRS.Simple
        },
        maxBounds: leafletBoundsHelpers.createBoundsFromArray(bounds),
        center: [0, 0],
        layers: {
            baselayers: {
                //The map image
                map: {
                    name: 'map',
                    type: 'imageOverlay',
                    url: carte.image,
                    bounds: bounds
                }
            }
        },
        controls: {
            custom: []
        }
    }

    /**
     * **************************
     * LOCAL METHODS
     * **************************
     */
    var onGetMap = function(map){
        $scope.map = map;
    }

    /**
     * **************************
     * EVENTS
     * **************************
     */
    leafletData.getMap().then(onGetMap);


    /**
     * **************************
     * BOOTSTRAP
     * **************************
     */
//    $scope.options.controls.custom.push(L.control.sidebar('sidebar'));

    console.log(carte);

}]);