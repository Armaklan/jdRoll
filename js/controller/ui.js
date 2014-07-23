/**
 * Activate all UI Basic Component
 *
 * @package notification
 * @copyright (C) 2014 jdRoll
 * @license MIT
 */
var uiControllerImpl = function() {

    var client;
    var keyIsDown = false;

    var changeLocation = function(url, ctrlKey) {
        if(url != '') {
            if(ctrlKey) {
                window.open(url);
            } else {
                window.location = url;
            }
        }
    };

    function activateSelect2() {

       $('.select2').select2({width: 'resolve'});

        $(document).on('keydown', function(e){
            keyIsDown = e.ctrlKey;
        });

        $(document).on('keyup', function(e){
            keyIsDown = e.ctrlKey;
        });

        $(document).on('keypress', function(e){
            keyIsDown = e.ctrlKey;
        });

       $('.navigationSelect').on('select2-selecting', function(val, object) {
           changeLocation(val.val, keyIsDown);
       });
    }

    function activateAffix() {
       $('.affix').affix({
           offset: {
               top: 200
           },
           bottom: function () {
               return ( this.bottom = $('.footer').outerHeight(true));
           }
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
        $('.focus-elt').focus();
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
        client = new ZeroClipboard( $('#btn-upload-copy'));
    }

    function associateCopyBtn() {
        client.on( 'load', function(client) {

        });
        client.on( 'complete', function(client, args) {
            // `this` is the element that was clicked
            alert('Url copié dans le presse papier ');
        });

    }

    function activateCarousel() {
        $('.carousel-control.right').trigger('click');
    }

    return {
        activateUi : function() {
            activateSelect2();
            activateAffix();
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
