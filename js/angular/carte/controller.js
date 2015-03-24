/**
 * TODO / To check&test
 * https://github.com/SINTEF-9012/Leaflet.MapPaint
 * https://github.com/ablakey/Leaflet.SimpleGraticule <== GRID
 * https://github.com/ubergesundheit/Leaflet.EdgeMarker
 * http://jawj.github.io/OverlappingMarkerSpiderfier-Leaflet/demo.html
 * https://github.com/CliffCloud/Leaflet.EasyButton/tree/v0
 */
ngApplication.controller('CtrlCarte', ['$scope', '$http', '$timeout', 'leafletData', 'leafletLayerHelpers', 'leafletBoundsHelpers', 'leafletMarkersHelpers', 'promiseTracker', 'carte',
function ($scope, $http, $timeout, leafletData, leafletLayerHelpers, leafletBoundsHelpers, leafletMarkersHelpers, promiseTracker, carte) {

    /**
     * **************************
     * LOCAL VARIABLES
     * **************************
     */

    /**
     * **************************
     * SCOPE VARIABLES
     * **************************
     */
        //Carte received
    $scope.carte = carte;
    //Tracker to show loading icon
    $scope.tracker = promiseTracker();
    //Map options
    $scope.options = {
        defaults: {
            minZoom: -4,
            maxZoom: 4,
//            zoom: 20,
            crs: L.CRS.Simple,
            attributionControl: false,
            zoomControl: false
        },
        center: [0, 0],
        markers: {},
        layers: {
            baselayers: {
            }
        },
        controls: {
            custom: []
        }
    }
    //Droppable options
    $scope.dropOptions = {
        onDrop: 'onDropItem'
    }

    //Default tab shown
    $scope.carte.config.tab = $scope.carte.config.tab ? 'perso':$scope.carte.config.tab;

    //Interface object, used in the template only
    $scope.interface = {
        image: $scope.carte.image
    };

    /**
     * **************************
     * LOCAL METHODS
     * **************************
     */
    var onGetMap = function(map){
        //Put map object in scope
        $scope.map = map;
        imageSetup();
    }

    /**
     * This function can be called once the image dimensions are know.
     * - It will load the image on the map, removing the one before if necessary
     * - It will then fit the map to the image
     */
    var imageSetup = function(){
        //Bounds of the map from image dimensions
        var bounds = [
            [-$scope.carte.dimensions.h/2, -$scope.carte.dimensions.w/2],
            [$scope.carte.dimensions.h/2, $scope.carte.dimensions.w/2]
        ];

        //Offset to allow moving the map to the side
        var offsetBounds = Math.max($scope.carte.dimensions.h, $scope.carte.dimensions.w);

        //Compute max bounds
        $scope.options.maxBounds = leafletBoundsHelpers.createBoundsFromArray([
            [-carte.dimensions.h/2-offsetBounds, -carte.dimensions.w/2-offsetBounds],
            [carte.dimensions.h/2+offsetBounds, carte.dimensions.w/2+offsetBounds]
        ]);

        //Remove layers
        $scope.map.eachLayer(function (layer) {
            if(layer._image){
                $scope.map.removeLayer(layer);
            }
        });

        //Add the image layer
        var layer = leafletLayerHelpers.createLayer({
            name: 'map',
            type: 'imageOverlay',
            url: $scope.carte.image,
            bounds: bounds
        });
        $scope.map.addLayer(layer);

        //Fit image to the map
        $timeout(function(){
            $scope.map.fitBounds(bounds);
            $scope.map.once('zoomend', function(){
                $scope.map.options.minZoom = $scope.map.getZoom();
                $scope.map.options.maxZoom = $scope.map.getZoom() + 4;
            });
        })

    }

    /**
     * General marker creation
     * @param id
     * @param type
     * @param position
     * @param image
     * @param title
     * @param cls
     * @param popup
     * @returns {{lat: (.createBoundsFromArray.northEast.lat|*|.createBoundsFromArray.southWest.lat|c.center.lat|e.northEast.lat|e.southWest.lat), lng: (.createBoundsFromArray.northEast.lng|*|.createBoundsFromArray.southWest.lng|c.center.lng|e.northEast.lng|e.southWest.lng), title: *, icon: {type: string, iconSize: number[], iconAnchor: number[], popupAnchor: number[], html: string, className: string}, draggable: boolean}}
     */
    var createMarker = function(id, type, position, image, title, cls, popup){
        //Create base marker
        var marker = {
            lat: position[0],
            lng: position[1],
            title: title,
            type: type,
            icon: {
                type: 'div',
                iconSize: [48, 48],
                iconAnchor: [24, 24],
                popupAnchor:  [0, 0],
                html: !_.isEmpty(image) ? '<img src="'+image+'"/>':'',
                className: 'map-' + type + ' ' + cls
            },
            draggable: $scope.carte.isMj ? true:false
        }

        //Create popup & its scope
        var scope = $scope.$new();
        var popupFile = 'popup-'+type+'-'+($scope.carte.isMj === true ? 'mj':'player')+'.html';
        scope.popup = popup && !_.isArray(popup) ? popup:{};
        scope.marker = marker;

        //Keep reference of scope info on marker
        marker.popup = scope.popup;
        marker.getMessageScope = function(){
            return scope;
        };
        marker.message = "<div class=\"map-popup map-popup-"+type+"\" ng-include src=\"'js/angular/carte/"+popupFile+"'\"></div>";

        //Create marker on map
        $scope.options.markers[id] = marker;

        //Return created information
        return {
            marker: marker,
            scope: scope
        };
    }

    /**
     * Shortcut to create a perso marker
     * @param perso
     * @param position
     */
    var createMarkerPerso = function(perso, position, popup){
        var created = createMarker('p' + perso.id, 'perso', position, 'files/thumbnails/perso_' + perso.id + '.png', perso.name, perso.user_id ? 'map-perso-user':'', popup);
        created.marker.id = perso.id;
        created.scope.perso = perso;
        perso.onMap = true;
    }

    /**
     * Shortcut to create a custom marker
     * @param perso
     * @param position
     */
    var createMarkerCustom = function(id, position, popup){
        var id = id ? id:Math.max(_.max(_.pluck(_.where($scope.options.markers, {type: 'custom'}), 'id')), 0)+1;
        var created = createMarker('custom' + id, 'custom', position, '', 'Marqueur Personnalisé', '', popup);
        created.marker.id = id;
        created.scope.id = id;
    }

    /**
     * Serialises the markers of the map on the carte object
     */
    var updateCarteMarkers = function(){
        //Create marker array
        var markers = [];
        _.each($scope.options.markers, function(marker){
            markers.push({
                type: marker.type,
                id: marker.id,
                position: [marker.lat, marker.lng],
                popup: marker.popup
            });
        });

        //Save if changes
        if( ! _.isEqual($scope.carte.config.markers, markers) || force){
            $scope.carte.config.markers = markers;
        }
    }

    /**
     * When changing the image
     * @param newImage
     * @param oldImage
     */
    var onImageChange = function(newImage, oldImage){
        if(newImage != oldImage){
            $scope.getImageDimension(newImage).then(function(dimensions){
                if(dimensions.w && dimensions.h){
                    $scope.carte.dimensions = dimensions;
                    $scope.carte.image = newImage;
                    imageSetup();
                }
            });
        }
    }

    /**
     * Save the map
     * @type {Function}
     */
    var saveMap = _.debounce(function(force){
        var request = $http.post('carte/save', $scope.carte);
        $scope.tracker.addPromise(request);
    }, 500);

    /**
     * **************************
     * SCOPE METHODS
     * **************************
     */
    //Dropping an item on the map
    $scope.onDropItem = function(event, ui){
        var imageOffset = ui.draggable.width()/2;
        var mapOffset = $($scope.map.getContainer()).offset();
        var scope = ui.draggable.scope();
        var p = $scope.map.containerPointToLatLng([
            ui.offset.left - mapOffset.left + imageOffset,
            ui.offset.top - mapOffset.top + imageOffset
        ]);
        if(scope.perso){
            createMarkerPerso(scope.perso, [p.lat, p.lng]);
        }
        else{
            createMarkerCustom(null, [p.lat, p.lng], {});
        }
    }

    //Removing a perso from the map
    $scope.removeMarkerPerso = function(perso){
        if(confirm('Êtes vous sûr(e) de vouloir retirer ce personnage de la carte?')){
            delete $scope.options.markers['p'+perso.id];
            perso.onMap = false;
        }
    }

    //Removing a custom marker from the map
    $scope.removeMarkerCustom = function(marker){
        if(confirm('Êtes vous sûr(e) de vouloir retirer ce marqueur personnalisé de la carte?')){
            delete $scope.options.markers['custom'+marker.id];
        }
    }

    /**
     * Select a tab
     * @param tab
     */
    $scope.selectTab = function(tab){
        $scope.carte.config.tab = tab;
        $scope.carte.config.tabReduce = false;
    }

    /**
     * **************************
     * BOOTSTRAP
     * **************************
     */
    //If we have some markers to load, do it
    if($scope.carte.config.markers){
        $scope.carte.config.markers.forEach(function(marker){
            if(marker.type == 'perso'){
                createMarkerPerso(_.findWhere($scope.carte.personnages, {id : marker.id}), marker.position, marker.popup);
            }
            else if(marker.type == 'custom'){
                createMarkerCustom(marker.id, marker.position, marker.popup);
            }
        });
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
            updateCarteMarkers();
        }
    }, true);
    $scope.$watch(function(){return $scope.carte}, saveMap, true);
    $scope.$watch(function(){return $scope.interface.image}, onImageChange, true);


}]);