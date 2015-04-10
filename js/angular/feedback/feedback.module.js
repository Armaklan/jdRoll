(function() {
  "use strict";

  angular
  .module('jdRoll.feedback', ['ui.tinymce', 'ngSanitize'])
  .controller('FeedbackController', FeedbackController)
  .service('Feedback', Feedback)
  .config(routeConfig);

  function FeedbackController($http, Feedback) {

    var ctrl = this;
    ctrl.feedback = {};
    ctrl.listurl = window.BASE_PATH + '/feedback/';
    ctrl.push = push;
    ctrl.isOk = false;
    initTinymceOptions();

    function push() {
      Feedback.push(ctrl.feedback).then(onValidFeedback(), function(ret) {
        // On error
        ctrl.msg = ret.data;
      });
    }

    function onValidFeedback() {
      ctrl.msg = "";
      ctrl.isOk = true;
      ctrl.feedback = {};
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

  function Feedback($http) {
    this.push = push;

    function push(feedback) {
       return $http({
          url: 'feedback/',
          method: 'POST',
          data: feedback
        }).then(function(ret) {
          return ret.data;
        });
    }

  }


  function routeConfig($stateProvider) {
    $stateProvider.state('feedback', {
      templateUrl: 'js/angular/feedback/feedback.html',
      controller: 'FeedbackController',
      controllerAs: 'feedbackCtrl'
    });
  }
})();
