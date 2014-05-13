var feedbackService = (function(){
    "use strict";

    var component = {};

    var getModal = function() {
        return $("#feedbackPopup");
    };

    component.openModal = function() {
        getModal().modal('show');
    };

    return component;
})();
