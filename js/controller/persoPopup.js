var persoModalService = (function () {
    "use strict";

    var component = {};

    var baseUrl = BASE_PATH + "/perso/ajax/";
    var widgetData;
    var baseId;
    var baseCampagne;

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

    var attachWidgetEvent = function() {
      var data = $("#genPersoModal #widget-data").val();
      if(data) {
        widgetData = JSON.parse(data);
        $("#genPersoModal .minusWidget").on('click', function() {
          minusWidget($(this));
        });
        $("#genPersoModal .addWidget").on('click', function() {
          addWidget($(this));
        });
      }
    }

    component.openPerso = function(campagne, id) {
        getModalName().html("Chargement en cours...");
        getModalContent().html("");
        baseCampagne = campagne;
        baseId = id;
        getData(campagne,id).
        done(function(data) {
            getModalContent().html(data.content);
            getModalName().html(data.name);
            attachWidgetEvent();
        });
        getModal().modal('show');
    };

    function minusWidget(elt) {
      var id = elt.attr('data-widget-id');
      widgetData.forEach(function(w) {
        if(w.id === id) {
          w.value = parseInt(w.value) - 1;
          updateComponent(w);
          updateWidgetDb();
        }
      });

    }

    function addWidget(elt) {
      var id = elt.attr('data-widget-id');
      widgetData.forEach(function(w) {
        if(w.id === id)  {
          w.value = parseInt(w.value) + 1;
          updateComponent(w);
          updateWidgetDb();
        }
      });
    }

    function updateComponent(widget) {
      if(widget.type == "jauge") {
        updateProgressBar(widget);
      } else if(widget.type == "token") {
        updateTokenBar(widget);
      }
    }

    function updateProgressBar(widget) {
      var bar = $('#genPersoModal .progress-bar[data-widget-id="' + widget.id + '"]');
      var width = (100 * (widget.value - widget.low) ) / (widget.up - widget.low);
      bar.width( width + "%" );
      bar.html( widget.value + " / " + widget.up);
    }

    function updateTokenBar(widget) {
      var bar = $('#genPersoModal .token-bar[data-widget-id="' + widget.id + '"]');
      var content = "";
      if(widget.value > 0) {
        for(var i = 0; i < widget.value; i++) {
          content = content + "<span class='icon-circle'></span> ";
        }
        bar.html(content);
      } else {
        bar.html("Aucun");
      }
    }

    function updateWidgetDb() {
      $.ajax({
          type: "POST",
          url: baseUrl + baseCampagne + "/widget/" + baseId,
          data: JSON.stringify(widgetData),
          dataType: "json"
      });
    }

    return component;
})();
