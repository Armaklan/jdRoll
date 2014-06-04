/**
 * Activate all UI Basic Component
 *
 * @package notification
 * @copyright (C) 2014 jdRoll
 * @license MIT
 */
var uiControllerImpl = function() {

    var client;

    var changeLocation = function(url) {
        if(url != "") {
            window.location = url;
        }
    };

    function activateSelect2() {

       $('.select2').select2({width: 'resolve'});
       $('.navigationSelect').on("select2-selecting", function(val, object) {
           changeLocation(val.val);
       });
    }

    function activateColorpicker() {
       $('.colorpicker').colorpicker();
    }

    function activateTooltip() {
        $('.iconeBtn').tooltip();
        $('.popover-elt').popover();
    }

    function autofocus() {
        $(".focus-elt").focus();
    }

    function resizeSidebar() {
        if($(window).width() > 992) {
            $('.maxheight').css({'height':($(window).height() - 50)+'px'});
            $('.maxheight').css({'overflow':'auto'});
        } else {
            $('.maxheight').css({'height':'100%'});
            $('.maxheight').css({'overflow':'visible'});
        }
    }

    function activateSidebarLayout() {
        resizeSidebar();
        $(window).resize(function(){
            resizeSidebar();
        });
    }


    function activateZeroClipboard() {
        ZeroClipboard.config( { moviePath: BASE_PATH + '/vendor/zeroclipboard/ZeroClipboard.swf' } );
        client = new ZeroClipboard( $("#btn-upload-copy"));
    }

    function associateCopyBtn() {
        client.on( "load", function(client) {

        });
        client.on( "complete", function(client, args) {
            // `this` is the element that was clicked
            alert("Url copié dans le presse papier ");
        });

    }

    function activateCarousel() {
        $('.carousel-control.right').trigger('click');
    }

    return {
        activateUi : function() {
            activateSelect2();
            activateSidebarLayout();
            activateZeroClipboard();
            associateCopyBtn();
            activateTooltip();
            autofocus();
            activateCarousel();
            activateColorpicker();
        }
    };
};

var uiController = uiControllerImpl();
onLoadController.generals.push(uiController.activateUi);
