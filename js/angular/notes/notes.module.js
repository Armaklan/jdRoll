(function() {
  "use strict";

  angular
  .module('jdRoll.notes', ['ui.tinymce', 'ngSanitize'])
  .controller('NotesController', NotesController)
  .service('Note', Note);

  function NotesController($http, Note) {

    var ctrl = this;
    ctrl.update = update;
    ctrl.add = add;
    ctrl.remove = remove;
    initTinymceOptions();
    initNotes();

    function initNotes() {

      Note.get().then(function(data){
        ctrl.noteslist = data;
      });
    }

    function add() {
      ctrl.noteslist.unshift({
        id: 0,
        content: "",
        edit: true
      });
    }

    function update(note) {
      note.edit = false;
      Note.update(note).then(function(data) {
        note.id = data.id;
      });
    }

    function remove(note) {
      ctrl.noteslist = ctrl.noteslist.filter(function(obj) {
        return obj !== note;
      });
      Note.remove(note);
    }

    function initTinymceOptions() {
      ctrl.tinymceOptions = {
        plugins: [
            "link image lists table"

        ],
        content_css : window.BASE_PATH + "/css/main.css",
        browser_spellcheck: true,
        menubar: false,
        convert_urls: false,
        toolbar: "styleselect removeformat | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        style_formats: formats,
        autosave_ask_before_unload: false
      };
    }
  }

  function Note($http) {
    this.get = get;
    this.update = update;
    this.remove = remove;

    function get() {
      return $http({
        url: 'notes/' + window.CAMPAGNE_ID + '/content'
      }).then(function(ret) {
        return ret.data;
      });
    }

    function update(note) {
       return $http({
          url: 'notes/' + window.CAMPAGNE_ID,
          method: 'POST',
          data: note
        }).then(function(ret) {
          return ret.data;
        });
    }

    function remove(note) {
      return $http({
        url: 'notes/' + window.CAMPAGNE_ID,
        method: 'DELETE',
        data: note
      });
    }
  }
})();
