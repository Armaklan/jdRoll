/**
 * TODO / To check&test
 * https://github.com/SINTEF-9012/Leaflet.MapPaint
 * https://github.com/ubergesundheit/Leaflet.EdgeMarker
 * http://jawj.github.io/OverlappingMarkerSpiderfier-Leaflet/demo.html
 */
ngApplication.controller('CtrlCarteManager', ['$scope', '$http', '$timeout', 'leafletData', 'leafletLayerHelpers', 'leafletBoundsHelpers', 'leafletMarkersHelpers', 'carte',
function ($scope, $http, $timeout, leafletData, leafletLayerHelpers, leafletBoundsHelpers, leafletMarkersHelpers, carte) {

    /**
     * ********************************************************************************************************
     * ********************************************************************************************************
     * LOCAL VARIABLES
     * ********************************************************************************************************
     * ********************************************************************************************************
     */

    /**
     * ********************************************************************************************************
     * ********************************************************************************************************
     * SCOPE VARIABLES
     * ********************************************************************************************************
     * ********************************************************************************************************
     */
    //Carte received
    $scope.carte = angular.copy(carte);

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

    //Interface object, used in the template only
    $scope.interface = {
        image: $scope.carte.image,
        tab: 'perso'
    };

    /**
     * ********************************************************************************************************
     * ********************************************************************************************************
     * LOCAL METHODS
     * ********************************************************************************************************
     * ********************************************************************************************************
     */

    /**
     * Callback once the map has loaded
     * @param map
     */
    var onGetMap = function(map){
        //Put map object in scope
        $scope.map = map;
        //Launch setup
        imageSetup();
    }

    /**
     * This function can be called once the image dimensions are know.
     * - It will load the image on the map, removing the one before if necessary
     * - It will then fit the map to the image
     * @return L.ImageOverlay
     */
    var imageSetup = function(){
        //Bounds of the map from image dimensions
        var bounds = [
            [-$scope.carte.dimensions.h/2, -$scope.carte.dimensions.w/2],
            [$scope.carte.dimensions.h/2, $scope.carte.dimensions.w/2]
        ];

        //Offset to allow moving the map to the side
        var offsetBounds = Math.max($scope.carte.dimensions.h, $scope.carte.dimensions.w);

        //Rest min & max zoom
        $scope.map.options.minZoom = -4;
        $scope.map.options.maxZoom = +4;

        //Compute max bounds
        $scope.map.setMaxBounds([
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

        return layer;
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
        //Unique ID
        var uniqueId = type + id;

        //Create base marker
        var marker = {
            lat: position[0],
            lng: position[1],
            title: title,
            type: type,
            icon: {
                type: 'div',
                iconSize: [48, 48],
                iconAnchor: [24, 60],
                popupAnchor:  [0, 0],
                html: !_.isEmpty(image) ? '<img src="'+image+'"/>':'',
                className: 'map-pin map-pin-' + type + ' ' + cls
            },
            draggable: $scope.carte.isMj ? true:false
        }

        //Create popup & its scope
        var scope = $scope.$new();
        var popupFile = 'popup-'+($scope.carte.isMj === true ? 'mj':'player')+'-'+type+'.html';
        scope.popup = popup && !_.isArray(popup) ? popup:{};
        scope.marker = marker;

        //Keep reference of scope info on marker
        marker.popup = scope.popup;
        marker.getMessageScope = function(){
            return scope;
        };
        marker.message = "<div class=\"map-popup map-popup-"+type+"\" ng-include src=\"'js/angular/carte/"+popupFile+"'\"></div>";

        //Create marker on map
        $scope.options.markers[uniqueId] = marker;

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
        var created = createMarker(perso.id, 'perso', position, 'files/thumbnails/perso_' + perso.id + '.png', perso.name, perso.user_id ? 'map-pin-perso-user':'', popup);
        created.marker.id = perso.id;
        created.marker.perso = perso;
        perso.onMap = true;
    }

    /**
     * Remove a marker from the map
     * @param marker
     */
    var removeMarker = function(marker){
        if(marker.type == 'perso'){
            marker.perso.onMap = false;
        }
        delete $scope.options.markers[marker.type+marker.id];
    }

    /**
     * Shortcut to create a custom marker
     * @param perso
     * @param position
     */
    var createMarkerCustom = function(id, position, popup){
        var id = id ? id:Math.max(_.max(_.pluck(_.where($scope.options.markers, {type: 'custom'}), 'id')), 0)+1;
        var created = createMarker(id, 'custom', position, popup.image, 'Marqueur Personnalisé', '', popup);
        created.marker.id = id;
        created.scope.id = id;

        //Watch changes to image to update the icon
        $scope.$watch(function(){return created.scope.popup.image}, function(){
            created.marker.icon.html = '<img src="'+created.scope.popup.image+'"/>';
        }, true)
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
        if( ! _.isEqual($scope.carte.config.markers, markers)){
            $scope.carte.config.markers = markers;
        }
    }

    /**
     * Save the map
     * @type {Function}
     */
    var saveMap = _.debounce(function(){
        //Clone carte
        var copy = angular.copy($scope.carte);
        delete copy.personnages;

        if(copy.image == carte.image){
            //Send image url only if different then original image
            delete copy.image;
        }
        else{
            //Else new image becomes the original
            carte.image = $scope.carte.image;
        }
        $http.post('carte/save', copy);
    }, 500);

    /**
     * When changing markers
     * @param newValue
     * @param oldValue
     */
    var onMarkersChange = function(newValue, oldValue){
        if( ! _.isEqual(newValue, oldValue)){
            //Compare object to avoid saving on map initialisation
            updateCarteMarkers();
        }
    }

    /**
     * When changing the carte object
     */
    var onCarteChange = function(){
        saveMap();
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
                    //Change carte information
                    $scope.carte.dimensions = dimensions;
                    $scope.carte.image = newImage;
                    //Launch image setup
                    var layer = imageSetup();

                    //Place marker outside the map at the border
                    var bounds = layer._bounds;
                    var corner = bounds.getNorthEast();
                    _.each($scope.options.markers, function(marker, id){
                        if( ! bounds.contains([marker.lat, marker.lng])){
                            marker.lat = corner.lat;
                            marker.lng = corner.lng;
                        }
                    });
                }
            });
        }
    }

    /**
     * ********************************************************************************************************
     * ********************************************************************************************************
     * SCOPE METHODS
     * ********************************************************************************************************
     * ********************************************************************************************************
     */
    
    /**
     * Dropping an item on the map
     * @param event
     * @param ui
     */
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
            createMarkerCustom(null, [p.lat, p.lng], {image: 'img/defaultCustom.png'});
        }
        $scope.onDragEnd();
    }

    /**
     * On dragging
     */
    $scope.onDrag = function(){
        jQuery('.tooltip').hide();
        jQuery('.map-sidebar').css('background-color', 'rgba(255,255,255, 0.2)');
    }

    /**
     * On drag ending
     */
    $scope.onDragEnd = function(){
        jQuery('.map-sidebar').css('background-color', '#fff');
    }

    /**
     * Removing a perso from the map
     * @param perso
     */
    $scope.removeMarker = function(marker){
        if(confirm('Êtes vous sûr(e) de vouloir retirer ce marqueur de la carte?')){
            removeMarker(marker);
        }
    }

    /**
     * Select a tab
     * @param tab
     */
    $scope.selectTab = function(tab){
        $scope.interface.tab = tab;
        $scope.carte.config.tabReduce = false;
    }

    /**
     * ********************************************************************************************************
     * ********************************************************************************************************
     * BOOTSTRAP
     * ********************************************************************************************************
     * ********************************************************************************************************
     */
    //If we have some markers to load on the map, do it
    if($scope.carte.config.markers){
        $scope.carte.config.markers.forEach(function(marker){
            if(marker.type == 'perso'){
                var perso = _.findWhere($scope.carte.personnages, {id : marker.id});
                if(perso){
                    createMarkerPerso(perso, marker.position, marker.popup);
                }
            }
            else if(marker.type == 'custom'){
                createMarkerCustom(marker.id, marker.position, marker.popup);
            }
        });
    }

    /**
     * ********************************************************************************************************
     * ********************************************************************************************************
     * EVENTS LINKING
     * ********************************************************************************************************
     * ********************************************************************************************************
     */
    leafletData.getMap().then(onGetMap);
    if($scope.carte.isMj === true){
        $scope.$watch(function(){return $scope.options.markers}, onMarkersChange, true);
        $scope.$watch(function(){return $scope.carte}, onCarteChange, true);
        $scope.$watch(function(){return $scope.interface.image}, onImageChange, true);
    }


}]);