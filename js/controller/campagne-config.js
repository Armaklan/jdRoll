var campagneConfig = (function () {
    "use strict";

    var component = {};

    var baseUrl = BASE_PATH + "/campagne/";

    var getUrl = function(campagne, type) {
        return baseUrl + campagne + "/" + type;
    };

    var saveDescription = function(campagne, data) {
        return $.ajax({
            type: "POST",
            url: getUrl(campagne, 'desc'),
            data: data
        });
    };

    var clearMsg = function() {
        $('#campagneMsg').html('');
    };

    var setMsg = function(type, msg) {
        $('#campagneMsg').html("<div class='alert alert-" +
                               type + "'> " + msg + "</div>");
    };

    component.saveDescription = function(campagne) {
        var data = $('#gameDescForm').serialize();
        saveDescription(campagne, data).
        done(function() {
            setMsg("success", "Campagne sauvegardé");
        }).
        fail(function(err) {
            setMsg("danger", err);
        });
        return false;
    };

    return component;
})();
