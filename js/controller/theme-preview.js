var themeService = (function () {
    "use strict";

    var component = {};

    var updateSidebarColor = function(color) {
        $('.categorie').css('background-color', color);
        $('.sidebar').css('background-color', color);
    };

    var updateLinkColor = function(color) {
        $('#theme-container a').css('color', color);
    };

    var updateSidebarLinkColor = function(color) {
        $('.sidebar a').css('color', color);
        $('.sidebar').css('color', color);
        $('a.sidebarBtn').css('color', 'black');
    };

    var updateTextColor = function(color) {
        $('#theme-container').css('color', color);
    };

    var updateOdd = function(color) {
        $('.table-striped tbody > tr:nth-child(2n+1) > td').css('background-color', color);
        $('.table-striped tbody > tr:nth-child(2n+1) > th').css('background-color', color);
    };

    var updateEven = function(color) {
        $('.table-striped tbody > tr:nth-child(2n) > td').css('background-color', color);
        $('.table-striped tbody > tr:nth-child(2n) > th').css('background-color', color);
    };

    var updateDialogue = function(color) {
        $('.container-fluid .dialogue').css('color', color);
    };

    var updatePensee = function(color) {
        $('.container-fluid .pensee').css('color', color);
    };

    var updateRp1 = function(color) {
        $('.container-fluid .rp1').css('color', color);
    };

    var updateRp2 = function(color) {
        $('.container-fluid .rp2').css('color', color);
    };

    var updateQuote = function(color) {
        $('.container-fluid blockquote p').css('color', color);
    };

    var updateHr = function() {
        var value = $('#hr').val();
        $('hr').css('background-image',"url('" + value + "')");
    };

    var updateBann = function() {
        var value = $('#bann').val();
        $('.logo_campagne').css('background-image',"url('" + value + "')");
    };

    component.refresh = function() {
        updateOdd($('#odd_line_color').val());
        updateEven($('#even_line_color').val());
        updateTextColor($('#text_color').val());
        updateSidebarColor($('#sidebar_color').val());
        updateLinkColor($('#link_color').val());
        updateSidebarLinkColor($('#link_sidebar_color').val());
        updateDialogue($('#dialogue_color').val());
        updatePensee($('#pensee_color').val());
        updateRp1($('#rp1_color').val());
        updateRp2($('#rp2_color').val());
        updateHr();
        updateBann();
        updateQuote($('#quote_color').val());
    };

    return component;
})();
