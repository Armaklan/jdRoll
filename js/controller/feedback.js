var feedbackService = (function(){
    "use strict";

    var component = {};

    var baseUrl = BASE_PATH + "/feedback/";

    var getUrlToPush = function() {
        return baseUrl;
    };

    var getUrlToClose = function(id) {
        return baseUrl + id;
    };

    var getModal = function() {
        return $("#feedbackPopup");
    };

    var getTitle = function() {
        return $('#feedbackPopup #feedbackTitle').val();
    };

    var getContent = function() {
        return $('#feedbackPopup #feedbackContent').val();
    };

    var resetModal = function() {
        $('#feedbackPopup #feedbackTitle').val('');
        tinyMCE.get('feedbackContent').setContent('');
    };

    var setModalMsg = function(msg) {
        return $("#feedbackPopup .msg").html(msg);
    };

    var setFeedbackMsg = function(msg) {
        return $("#feedbacksMsg").html(msg);
    };

    var saveFeedback = function() {
        tinyMCE.triggerSave();
        return $.ajax({
            type: "POST",
            url: getUrlToPush(),
            data: {
                title: getTitle(),
                content: getContent()
            }
        });
    };

    var closeFeedback = function(id) {
        return $.ajax({
            type: "DELETE",
            url: getUrlToClose(id)
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

    component.close = function(id) {
        setFeedbackMsg('');
        closeFeedback(id).
        done(function(){
            setFeedbackMsg('<div class="alert alert-success">Feedback modifié avec succès</div>');
            $('#feedback' + id).css('display', 'none');
        });
    };

    component.openModal = function() {
        getModal().modal('show');
    };

    return component;
})();
