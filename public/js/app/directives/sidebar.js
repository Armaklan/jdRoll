var app = angular.module("jdRollApp", []);

app.directive('resize', function ($window) {
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