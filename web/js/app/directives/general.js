(function(angular){

    var module = angular.module("jdRoll.directives.generals", []);

    module.directive('resize', function ($window) {
        return function (scope, element) {
            var w = angular.element($window);
            scope.getWindowDimensions = function () {
                return { 'h': w.height() };
            };
            scope.$watch(scope.getWindowDimensions, function (newValue, oldValue) {
                scope.windowHeight = newValue.h;

                scope.resizestyle = function () {
                    return {
                        'height': (newValue.h - 50) + 'px',
                    };
                };

            }, true);

            w.bind('resize', function () {
                scope.$apply();
            });
        }
    })

    module.directive('autoFillSync', function($timeout) {
       return {
          require: 'ngModel',
          link: function(scope, elem, attrs, ngModel) {
              var origVal = elem.val();
              $timeout(function () {
                  var newVal = elem.val();
                  if(ngModel.$pristine && origVal !== newVal) {
                      ngModel.$setViewValue(newVal);
                  }
              }, 500);
          }
       }
    });

    module.directive('autoFocus', function($timeout) {
       return {
          link: function(scope, elem, attrs, ngModel) {
              elem.focus();
          }
       }
    });

    module.directive('waitingImg', function() {
        return {
            scope: {
                waitingImg: '='
            },
            templateUrl: 'views/directives/waiting.html'
        };
    });

    module.directive('gamesBox', function() {
        return {
            replace: true,
            scope: {
                gamesBox: '='
            },
            templateUrl: 'views/directives/games-box.html'
        };
    });

})(angular);
