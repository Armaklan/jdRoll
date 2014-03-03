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

    return {
        activateUi : function() {
            activateSelect2();
        }
    };
};

var uiController = uiControllerImpl();
onLoadController.generals.push(uiController.activateUi);