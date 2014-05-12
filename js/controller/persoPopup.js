var persoModalService = (function () {
    "use strict";

    var component = {};

    var baseUrl = BASE_PATH + "/perso/ajax/";


    var getUrl = function(campagne, id) {
        return baseUrl + campagne + "/" + id;
    }

    var getModal = function() {
        return $("#genPersoModal");
    };

    var getModalContent = function() {
        return $("#genPersoModal .modal-body");
    };

    var getModalName = function() {
        return $("#genPersoModal .modal-name");
    };

    var getData = function(campagne, id) {
        return $.ajax({
            type: "GET",
            url: getUrl(campagne, id)
        });
    }

    component.openPerso = function(campagne, id) {
        getModalName().html("Chargement en cours...");
        getModalContent().html("");
        getData(campagne,id).
        done(function(data) {
            getModalContent().html(data.content);
            getModalName().html(data.name);
        });
        getModal().modal('show');
    };

    return component;
})();
