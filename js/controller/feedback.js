var feedbackService = (function(){
    "use strict";

    var component = {};

    var baseUrl = BASE_PATH + "/feedback/";

    var getUrlToPush = function(campagne, id) {
        return baseUrl;
    };

    var getModal = function() {
        return $("#feedbackPopup");
    };

    var getTitle = function() {
        return $('#feedbackPopup feedbackTitle').val();
    };

    var getContent = function() {
        return $('#feedbackPopup feedbackContent').val();
    };

    var resetModal = function() {
        $('#feedbackPopup feedbackTitle').val('');
        $('#feedbackPopup feedbackContent').val('');
    };

    var setModalMsg = function(msg) {
        return $("#feedbackPopup msg").html(msg);
    };

    var saveFeedback = function() {
        return $.ajax({
            type: "POST",
            url: getUrlToPush(),
            data: {
                title: getTitle(),
                content: getContent()
            }
        });
    };

    component.pushFeedback = function() {
        setModalMsg('');
        saveFeedback().
        done(function(){
            setModalMsg('<div class="alert alert-success">Merci pour votre contribution !</div>');
            resetModal();
        });
    };

    component.openModal = function() {
        getModal().modal('show');
    };

    return component;
})();
