var campagneConfig = (function (tinyMCE) {
    "use strict";

    var component = {};

    var baseUrl = BASE_PATH + "/campagne/";

    var getUrl = function(campagne, type) {
        return baseUrl + campagne + "/" + type;
    };

    var getUrlInscription = function(type, campagne, user) {
        return baseUrl + type + '/' + campagne + "/" + user;
    };

    var save = function(type, campagne, data) {
        return $.ajax({
            type: "POST",
            url: getUrl(campagne, type),
            data: data
        });
    };

    var inscription = function(type, campagne, user) {
        return $.ajax({
            type: "GET",
            url: getUrlInscription(type, campagne, user)
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
        tinyMCE.triggerSave();
        var data = $('#gameDescForm').serialize();
        save('desc', campagne, data).
        done(function() {
            setMsg("success", "Campagne sauvegardé");
        }).
        fail(function(err) {
            setMsg("danger", err);
        });
        return false;
    };

    component.saveSheet = function(campagne) {
        tinyMCE.triggerSave();
        var data = $('#gameSheetForm').serialize();
        save('sheet', campagne, data).
        done(function() {
            setMsg("success", "Feuille de personnage sauvegardé");
        }).
        fail(function(err) {
            setMsg("danger", err);
        });
        return false;
    };

    component.saveTheme = function(campagne) {
        var data = $('#gameThemeForm').serialize();
        save('theme',campagne, data).
        done(function() {
            setMsg("success", "Thème sauvegardé");
        }).
        fail(function(err) {
            setMsg("danger", err);
        });
        return false;
    };

    component.saveDivers = function(campagne) {
        tinyMCE.triggerSave();
        var data = $('#gameDiversForm').serialize();
        save('divers',campagne, data).
        done(function() {
            setMsg("success", "Campagne sauvegardé");
        }).
        fail(function(err) {
            setMsg("danger", err);
        });
        return false;
    };

    component.deleteParticipant = function(campagne, user) {
        bootbox.confirm("Désinscrire ce joueur ?", function(confirmed) {
            if(confirmed) {
                inscription('ban',campagne, user).
                done(function() {
                    setMsg("success", "Joueur désinscrit");
                    $('#inscription'+user).css('display', 'none');
                }).
                fail(function(err) {
                    setMsg("danger", err);
                });
            }
        });
        return false;
    };

    component.addParticipant = function(campagne, user) {
        inscription('valid', campagne, user).
        done(function() {
            $('#inscription'+ user + ' button').css('display', 'none');
            setMsg("success", "Joueur accepté");
        }).
        fail(function(err) {
            setMsg("danger", err);
        });
        return false;
    };

    return component;
})(tinyMCE);
