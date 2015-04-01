/**
 * Control the dicer component
 *
 * @package notification
 * @copyright (C) 2014 jdRoll
 * @license MIT
 */
var uiNotesImpl = function () {
    "use strict";

    /* global $ */
    var srv = {};
    srv.showModal = showModal;
    srv.init = init;


    function init() {
      $('.blocnote-btn').on('click', srv.showModal);
      srv.notesModal = $('#notesModal');
      srv.notesModalContent = $('#notesModal .modal-content');
    }

    function showModal() {
      srv.notesModal.modal('show');
      if( !srv.notesModalContent.hasClass('ng-scope') ) {
        angular.bootstrap('#notes-app', ['jdRoll.notes']);
      }
    }

    return srv;
};

var uiNotes = uiNotesImpl();
onLoadController.generals.push(uiNotes.init);
