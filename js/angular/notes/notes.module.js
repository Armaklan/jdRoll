(function() {
  "use strict";

  angular
  .module('jdRoll.notes', ['ui.tinymce'])
  .controller('NotesController', NotesController);

  function NotesController($http) {

    var ctrl = this;
    ctrl.update = update;
    initTinymceOptions();
    initNotes();

    function initNotes() {
      $http({
        url: 'notes/' + window.CAMPAGNE_ID + '/content'
      }).then(function(ret){
        ctrl.content = ret.data;
      });
    }

    function update() {
      $http({
        url: 'notes/' + window.CAMPAGNE_ID,
        method: 'POST',
        data: {content: ctrl.content}
      });
    }

    function initTinymceOptions() {
      ctrl.tinymceOptions = {
        plugins: [
            "link image lists table code"

        ],
        content_css : window.BASE_PATH + "/css/main.css",
        browser_spellcheck: true,
        convert_urls: false,
        toolbar: "cut copy paste | styleselect removeformat | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        style_formats: formats,
        autosave_ask_before_unload: false
      };
    }
  }
})();
