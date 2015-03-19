/**
 * Control Draft editor actions
 *
 * @package draft
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

var carteControllerImpl = function(url) {

//    var

    /**
     * TODO / To check&test
     * https://github.com/turbo87/sidebar-v2/
     * https://github.com/SINTEF-9012/Leaflet.MapPaint
     * https://github.com/yohanboniface/Leaflet.Storage/blob/master/test/index.html
     * https://github.com/ablakey/Leaflet.SimpleGraticule <== GRID
     * + ANGULAR LEAFLET
     */
    var init = function(){

        //
        var img = $('#image');

        // dimensions of the image
        var w = img.width(), //2000,
            h = img.height() //1500,

        var windowWidth = $(window).width();
        var windowHeight = $(window).width();
        var rapport = Math.max(1, Math.floor(Math.min(windowWidth/w, windowHeight/h)));

        // create the slippy map
        var map = L.map('image-map', {
            minZoom: 1,
            maxZoom: 4,
            center: [0, 0],
            zoom: 1,
            crs: L.CRS.Simple
        });

        // calculate the edges of the image, in coordinate space
        var southWest = map.unproject([0, h*rapport], map.getMaxZoom()-1);
        var northEast = map.unproject([w*rapport, 0], map.getMaxZoom()-1);

        var bounds = new L.LatLngBounds(southWest, northEast);

        // add the image overlay,
        // so that it covers the entire map
        L.imageOverlay(url, bounds).addTo(map);

        // tell leaflet that the map is exactly as big as the image
        map.setMaxBounds(bounds);
        setTimeout(function(){
            //Fit after the bounds are fixed
            map.fitBounds(bounds);
        }, 500);
    }




    return {
        init: init
    };
};
var carteController = carteControllerImpl('{{ carte.img }}');
onLoadController.generals.push(carteController.init);
