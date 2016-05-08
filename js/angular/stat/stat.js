(function() {
    angular
        .module('jdRoll.stat', ['gridshore.c3js.chart'])
        .service('Stat', StatService)
        .directive('jdStatByMonth', statByMonthDirective)
        .directive('jdStatUserByDay', statUserByDayDirective)
        .directive('jdStatByGame', statByGameDirective)
        .directive('jdStatUserByGame', statUserByGameDirective)
        .directive('jdStatByMonthFor', statByMonthForDirective);

    function ByMonthForStatCtrl($scope, Stat) {
        var ctrl = this;
        $scope.statCtrl = ctrl;

        Stat.byMonthFor($scope.campagne).then(function(data) {
            ctrl.byMonth = {
                period: data.map(function(r) {
                    var elt = r.dat.split(',');
                    var year = elt[0].substring(2,4);
                    return elt[1] + '/' + year;
                }).join(','),
                values: data.map(function(r) {
                    return r.cpt;
                }).join(',')
            };
        });
    }

    function ByMonthStatCtrl($scope, Stat) {
        var ctrl = this;
        $scope.statCtrl = ctrl;

        Stat.byMonth().then(function(data) {
            ctrl.byMonth = {
                period: data.map(function(r) {
                    var elt = r.dat.split(',');
                    var year = elt[0].substring(2,4);
                    return elt[1] + '/' + year;
                }).join(','),
                values: data.map(function(r) {
                    return r.cpt;
                }).join(',')
            };
        });
    }

    function UserByDayStatCtrl($scope, Stat) {
        var ctrl = this;
        $scope.statCtrl = ctrl;

        Stat.forUser().then(function(data) {
            var total = 0;
            ctrl.datas = data.map(function(r) {
                total = total + parseInt(r.cpt);
                var dat = r.dat.split('-');
                r.dat = dat[2] + "/" + dat[1] + "/" + dat[0].substring(2,4);
                r.cpt = total;
                return r;
            });
            ctrl.columns = [{
                id: "cpt",
                name: "Nombre de posts",
                type: "line"
            }, {
                id: "dat"
            }];
            ctrl.x = {
                id: 'dat',
                name: 'Date'
            };
        });

    }

    function UserByGameStatCtrl($scope, Stat) {
        var ctrl = this;
        var total = 0;
        $scope.statCtrl = ctrl;

        Stat.userByGame().then(function(data) {
            data.forEach(function(elt) {
                total = total + parseFloat(elt.cpt);
            });
            data = data.filter(function(elt) {
                return elt.game;
            });
            data.sort(function(elt1, elt2) {
                return parseFloat(elt2.cpt) - parseFloat(elt1.cpt);
            });
            data.length = 15;
            ctrl.byGame = data;
            ctrl.total = total;
        });

        ctrl.formatLegend = function(value) {
            var percent = 100 * value / total;
            return Math.round(percent * 100) / 100 + "%";
        };

        ctrl.formatLabel = function(value) {
            return value;
        };
    }

    function ByGameStatCtrl($scope, Stat) {
        var ctrl = this;
        var total = 0;
        $scope.statCtrl = ctrl;

        Stat.byGame().then(function(data) {
            data.forEach(function(elt) {
               total = total + parseFloat(elt.cpt);
            });
            data = data.filter(function(elt) {
                return elt.game;
            });
            data.sort(function(elt1, elt2) {
                return parseFloat(elt2.cpt) - parseFloat(elt1.cpt);
            });
            data.length = 15;
            ctrl.byGame = data;
            ctrl.total = total;
        });

        ctrl.formatLegend = function(value) {
            var percent = 100 * value / total;
            return Math.round(percent * 100) / 100 + "%";
        };

        ctrl.formatLabel = function(value) {
            return value;
        };
    }

    function StatService($http) {
        var srv = this;

        srv.byMonth = function() {
            return $http({
                method: 'GET',
                url: 'apiv2/stats/bymonth'
            }).then(function(res) {
                return res.data;
            });
        };

        srv.byMonthFor = function(campagne) {
            return $http({
                method: 'GET',
                url: 'apiv2/stats/bymonth/' + campagne
            }).then(function(res) {
                return res.data;
            });
        };

        srv.byGame = function() {
            return $http({
                method: 'GET',
                url: 'apiv2/stats/bygame'
            }).then(function(res) {
                return res.data;
            });
        };

        srv.userByGame = function() {
            return $http({
                method: 'GET',
                url: 'apiv2/stats/my/bygame'
            }).then(function(res) {
                return res.data;
            });
        };

        srv.forUser = function() {
            return $http({
                method: 'GET',
                url: 'apiv2/stats/my/byday'
            }).then(function(res) {
                return res.data;
            });
        };
    }

    function statByMonthDirective() {
        return {
            restrict: 'AE',
            templateUrl: 'js/angular/stat/bymonth.tpl.html',
            controller: ByMonthStatCtrl,
            scope: {}
        };
    }

    function statByMonthForDirective() {
        return {
            restrict: 'AE',
            templateUrl: 'js/angular/stat/bymonth.tpl.html',
            controller: ByMonthForStatCtrl,
            scope: {
                campagne: '='
            }
        };
    }

    function statByGameDirective() {
        return {
            restrict: 'AE',
            templateUrl: 'js/angular/stat/bygame.tpl.html',
            controller: ByGameStatCtrl,
            scope: {}
        };
    }

    function statUserByDayDirective() {
        return {
            restrict: 'AE',
            templateUrl: 'js/angular/stat/user-by-day.tpl.html',
            controller: UserByDayStatCtrl,
            scope: {}
        };
    }

    function statUserByGameDirective() {
        return {
            restrict: 'AE',
            templateUrl: 'js/angular/stat/bygame.tpl.html',
            controller: UserByGameStatCtrl,
            scope: {}
        };
    }
})();
