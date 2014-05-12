var themeService = (function () {
    "use strict";

    var component = {};

    var updateSidebarColor = function(color) {
        $('.categorie').css('background-color', color);
        $('.sidebar').css('background-color', color);
    }

    var updateLinkColor = function(color) {
        $('#theme-container a').css('color', color);
    }

    var updateSidebarLinkColor = function(color) {
        $('.sidebar a').css('color', color);
        $('.sidebar').css('color', color);
        $('a.sidebarBtn').css('color', 'black');
    }

    var updateTextColor = function(color) {
        $('#theme-container').css('color', color);
    }

    component.onSidebarColorChange = function(color) {
        updateSidebarColor(color);
    };

    component.onLinkColorChange = function(color) {
        updateLinkColor(color);
    };

    component.onSidebarLinkColorChange = function(color) {
        updateSidebarLinkColor(color);
    };

    component.onTextColorChange = function(color) {
        updateTextColor(color);
    };

    component.refresh = function() {
        updateTextColor($('#text_color').val());
        updateSidebarColor($('#sidebar_color').val());
        updateLinkColor($('#link_color').val());
        updateSidebarLinkColor($('#link_sidebar_color').val());
    };

    return component;
})();
