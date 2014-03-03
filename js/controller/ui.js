/**
 * Activate all UI Basic Component
 *
 * @package notification
 * @copyright (C) 2014 jdRoll
 * @license MIT
 */
var uiControllerImpl = function() {

    function activateSelect2() {
       $('.select2').select2({width: 'element'});
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

    return {
        activateUi : function() {
            activateSelect2();
            activateSidebarLayout();
        }
    };
};

var uiController = uiControllerImpl();
onLoadController.generals.push(uiController.activateUi);