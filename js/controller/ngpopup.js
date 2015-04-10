/**
 * Control the dicer component
 *
 * @package notification
 * @copyright (C) 2014 jdRoll
 * @license MIT
 */
var uiNgPopupImpl = function () {
    "use strict";

    /* global $ */
    var srv = {};
    srv.showNotes = showNotes;
    srv.showFeedback = showFeedback;
    srv.init = init;


    function init() {
      $('.blocnote-btn').on('click', srv.showNotes);
      srv.notesModal = $('#ngModal');
      srv.notesModalContent = $('#ngModal .modal-content');
    }

    function showNotes() {
      showModal('notes');
    }

    function showFeedback() {
      showModal('feedback');
    }

    function showModal(state) {
      srv.notesModal.modal('show');
      if( !srv.notesModalContent.hasClass('ng-scope') ) {
        angular.bootstrap('#popup-app', ['jdroll.popup']);
      }
      window.popupState(state);
    }

    return srv;
};

var uiNgPopup = uiNgPopupImpl();
onLoadController.generals.push(uiNgPopup.init);
