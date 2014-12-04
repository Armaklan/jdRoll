(function() {
    "use strict";

    var widgetsController = function($scope, $http) {
        var vm = this;

        buildVM();
        vm.add = add;
        vm.remove = remove;
        vm.save = save;

        function buildVM() {
            vm.widgets = $scope.widgets;
        }

        function add(type) {
            vm.widgets.push({
                id: guid(),
                type: type
            });
        }

        function remove(widget) {
            vm.widgets = vm.widgets.filter(function(item) {
                return item !== widget;
            });
        }

        function save() {
            if (vm.form.$valid) {
                $http({
                    url: BASE_PATH + '/campagne/' + $scope.campagne + '/perso_widgets',
                    method: 'POST',
                    data: vm.widgets
                }).then(function() {
                    vm.success = true;
                });
            }
        }

        function guid() {
            function s4() {
                return Math.floor((1 + Math.random()) * 0x10000)
                    .toString(16)
                    .substring(1);
            }
            return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
                s4() + '-' + s4() + s4() + s4();
        }
    };

    angular
        .module('jdRoll.WidgetPersoApp', ['ui.bootstrap'])
        .controller('WidgetsController', widgetsController);
})();
