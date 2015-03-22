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

    /**
     * General marker creation
     * @param id
     * @param position
     * @param image
     * @param title
     * @param cls
     * @returns {{lat: (.createBoundsFromArray.northEast.lat|*|.createBoundsFromArray.southWest.lat|c.center.lat|e.northEast.lat|e.southWest.lat), lng: (.createBoundsFromArray.northEast.lng|*|.createBoundsFromArray.southWest.lng|c.center.lng|e.northEast.lng|e.southWest.lng), title: *, icon: {type: string, iconSize: number[], iconAnchor: number[], popupAnchor: number[], html: string, className: string}, draggable: boolean}}
     */
    var createMarker = function(id, position, image, title, cls){
        return $scope.options.markers[id] = {
            lat: position[0],
            lng: position[1],
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
     * Shortcut to create a perso marker
     * @param perso
     * @param position
     */
    var createMarkerPerso = function(perso, position){
        var marker = createMarker('p' + perso.id, position, 'files/thumbnails/perso_' + perso.id + '.png', perso.name, perso.user_id ? 'map-perso-user':'');
        marker.type = 'perso';
        marker.id = perso.id;
        perso.onMap = true;
    }

    var saveMap = _.debounce(function(){
        $scope.carte.config.markers = [];
        _.each($scope.options.markers, function(marker){
            $scope.carte.config.markers.push({
                type: marker.type,
                id: marker.id,
                position: [marker.lat, marker.lng]
            })
        })
        $http.post('carte/save', $scope.carte);
    }, 1000)

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
        var p = $scope.map.containerPointToLatLng([
            ui.offset.left - mapOffset.left + imageOffset,
            ui.offset.top - mapOffset.top + imageOffset
        ]);
        var marker = createMarkerPerso(perso, [p.lat, p.lng]);
    }

    /**
     * **************************
     * EVENTS
     * **************************
     */
    leafletData.getMap().then(onGetMap);
    $scope.$watch(function(){return $scope.options.markers}, function(newValue, oldValue){
        if( ! _.isEqual(newValue, oldValue)){
            //Compare object to avoid saving on map initialisation
            saveMap();
        }
    }, true);

    /**
     * **************************
     * BOOTSTRAP
     * **************************
     */
    //If we have some markers to load, do it
    if($scope.carte.config.markers){
        $scope.carte.config.markers.forEach(function(marker){
            if(marker.type == 'perso'){
                createMarkerPerso(_.findWhere($scope.carte.personnages, {id : marker.id}), marker.position);
            }
        });
    }


}]);