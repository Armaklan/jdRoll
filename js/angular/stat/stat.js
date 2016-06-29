(function() {
    angular
        .module('jdRoll.stat', ['gridshore.c3js.chart'])
        .service('Stat', StatService)
        .controller('StatCtrl', StatCtrl)
        .directive('jdStatByMonth', statByMonthDirective)
        .directive('jdStatUserByDay', statUserByDayDirective)
        .directive('jdStatByGame', statByGameDirective)
        .directive('jdStatUserByGame', statUserByGameDirective)
        .directive('jdStatByMonthFor', statByMonthForDirective);

    function ModeStat(label, beginDate, selected) {
        this.label = label;
        this.beginDate = beginDate;
        this.selected = selected;
    }

    function StatCtrl() {
        var ctrl = this;
        ctrl.mode = [
            new ModeStat('Depuis le début', undefined),
            new ModeStat('Depuis un an', moment().subtract(1, 'years').format('YYYY-MM-DD')),
            new ModeStat('Depuis 6 mois', moment().subtract(6, 'month').format('YYYY-MM-DD'), true)
        ];
        ctrl.beginDate = moment().subtract(6, 'month').format('YYYY-MM-DD');

        ctrl.changeMode = function(mode) {
            ctrl.mode.forEach(function(currentMode) {
                currentMode.selected = false;
            });
            mode.selected = true;
            ctrl.beginDate = mode.beginDate;
        };
    }

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

        $scope.$watch('beginDate', function() {
            refresh();
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

        function refresh() {
            Stat.forUser($scope.beginDate).then(function(data) {
                var total = 0;
                ctrl.datas = data.map(function(r) {
                    total = total + parseInt(r.cpt);
                    var dat = r.dat.split('-');
                    r.dat = dat[2] + "/" + dat[1] + "/" + dat[0].substring(2,4);
                    r.cpt = total;
                    return r;
                });
            });
        }
    }

    function UserByGameStatCtrl($scope, Stat) {
        var ctrl = this;
        var total = 0;
        $scope.statCtrl = ctrl;

        $scope.$watch('beginDate', function() {
            refresh();
        });

        function refresh() {
            Stat.userByGame($scope.beginDate).then(function(data) {
                data.forEach(function(elt) {
                    total = total + parseFloat(elt.cpt);
                });
                data = data.filter(function(elt) {
                    return elt.game;
                });
                data.sort(function(elt1, elt2) {
                    return parseFloat(elt2.cpt) - parseFloat(elt1.cpt);
                });
                if(data.length > 15) data.length = 15;
                refreshColumn(data);
                ctrl.byGame = data;
                ctrl.total = total;
            });
        }

        function refreshColumn(data) {
            ctrl.columns = data.map(function(elt, index){
                return {
                    id: 'bygame' + index,
                    name: elt.game,
                    values: elt.cpt,
                    type: 'donut'
                };
            });
            var datas = {};
            ctrl.columns.forEach(function(elt){
                datas[elt.id] = elt.values;
            });
            ctrl.datas = [datas];
        }

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
        refresh();

        function refresh() {
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
                if(data.length > 15) data.length = 15;
                refreshColumn(data);
                ctrl.byGame = data;
                ctrl.total = total;
            });
        }

        function refreshColumn(data) {
            ctrl.columns = data.map(function(elt, index){
                return {
                    id: 'bygame' + index,
                    name: elt.game,
                    values: elt.cpt,
                    type: 'donut'
                };
            });
            var datas = {};
            ctrl.columns.forEach(function(elt){
                datas[elt.id] = elt.values;
            });
            ctrl.datas = [datas];
        }


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

        srv.userByGame = function(beginDate) {
            return $http({
                method: 'GET',
                url: 'apiv2/stats/my/bygame',
                params: {
                    beginDate: beginDate
                }
            }).then(function(res) {
                return res.data;
            });
        };

        srv.forUser = function(beginDate) {
            return $http({
                method: 'GET',
                url: 'apiv2/stats/my/byday',
                params: {
                    beginDate: beginDate
                }
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
            scope: {}
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
            scope: {
                beginDate:'='
            }
        };
    }

    function statUserByGameDirective() {
        return {
            restrict: 'AE',
            templateUrl: 'js/angular/stat/bygame.tpl.html',
            controller: UserByGameStatCtrl,
            scope: {
                beginDate:'='
            }
        };
    }
})();
