/**
 * TODO / To check&test
 * https://github.com/SINTEF-9012/Leaflet.MapPaint
 * https://github.com/ablakey/Leaflet.SimpleGraticule <== GRID
 * https://github.com/ubergesundheit/Leaflet.EdgeMarker
 * http://jawj.github.io/OverlappingMarkerSpiderfier-Leaflet/demo.html
 * https://github.com/CliffCloud/Leaflet.EasyButton/tree/v0
 */
ngApplication.controller('CtrlCarte', ['$scope', '$http', '$timeout', 'leafletData', 'leafletBoundsHelpers', 'leafletMarkersHelpers', 'carte',
    function ($scope, $http, $timeout, leafletData, leafletBoundsHelpers, leafletMarkersHelpers, carte) {

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
     * LOCAL METHODS
     * **************************
     */
    var onGetMap = function(map){
        //Put map object in scope
        $scope.map = map;

        //Add grid
        L.simpleGraticule({
            interval: 50,
            showOriginLabel: false,
            redraw: 'move'
        }).addTo(map);
    }

    var createMarker = function(id, position, image, title, cls){
        $scope.options.markers[id] = {
            lat: position.lat,
            lng: position.lng,
            title: title,
            icon: {
                type: 'div',
                iconSize: [48, 48],
                iconAnchor: [24, 24],
                popupAnchor:  [0, 0],
                html: '<img src="'+image+'"/>',
                className: 'map-perso ' + cls
            },
            draggable: true
        }
    }

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
        markers: {},
        layers: {
            baselayers: {
                //The map image
                map: {
                    name: 'map',
                    type: 'imageOverlay',
                    url: $scope.carte.image,
                    bounds: bounds
                }
            }
        },
        controls: {
            custom: []
        }
    }

    $scope.dropOptions = {
        onDrop: 'onDropItem'
    }


    /**
     * **************************
     * SCOPE METHODS
     * **************************
     */
    $scope.onDropItem = function(event, ui){
        var imageOffset = ui.draggable.width()/2;
        var mapOffset = $($scope.map.getContainer()).offset();
        var perso = ui.draggable.scope().perso;
        var position = $scope.map.containerPointToLatLng([
            ui.offset.left - mapOffset.left + imageOffset,
            ui.offset.top - mapOffset.top + imageOffset
        ]);
        perso.onMap = true;
        createMarker('p' + perso.id, position, 'files/thumbnails/perso_' + perso.id + '.png', perso.name, perso.user_id ? 'map-perso-user':'');
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


}]);