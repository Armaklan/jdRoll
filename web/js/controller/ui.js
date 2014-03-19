/**
 * Activate all UI Basic Component
 *
 * @package notification
 * @copyright (C) 2014 jdRoll
 * @license MIT
 */
var uiControllerImpl = function() {

    var client;

    function activateSelect2() {
       $('.select2').select2({width: 'element'});
    }

    function activateColorpicker() {
       $('.colorpicker').colorpicker();
    }

    function activateTooltip() {
        $('.iconeBtn').tooltip();
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

    return {
        activateUi : function() {
            activateSelect2();
            activateSidebarLayout();
            activateZeroClipboard();
            associateCopyBtn();
            activateColorpicker();
            activateTooltip();
            autofocus();
        }
    };
};

var uiController = uiControllerImpl();
onLoadController.generals.push(uiController.activateUi);