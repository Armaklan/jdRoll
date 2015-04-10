(function() {
  "use strict";

  angular.module('jdroll.popup', [
      'ui.router',
      'ajoslin.promise-tracker',
      'mgcrea.ngStrap',
      'ui.tinymce',
      'ngSanitize',
      // Application module
      'jdRoll.notes',
      'jdRoll.feedback'
  ])
  .run(exposeGoStep)
  .run(initLoadingTracker);

  function initLoadingTracker($rootScope, promiseTracker) {
    $rootScope.loadingTracker = promiseTracker();
  }

  function exposeGoStep($state) {
    window.popupState = function(stateName) {
      $state.go(stateName);
    };
  }

})();
