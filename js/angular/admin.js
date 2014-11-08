(function(){
    "use strict";

    var annoncesController = function(AnnoncesService, $modal) {
        var self=this;

        AnnoncesService.all().success(function(data){
           self.annonces = data;
        });

        var openModal = function(annonce) {
            var modalInstance = $modal.open({
                templateUrl: 'annonceModalContent.html',
                controller: 'ModalAnnonceController as ModalCtrl',
                size: 'lg',
                resolve: {
                    annonce: function () {
                        return angular.copy(annonce);
                    }
                }
            });

            modalInstance.result.then(function (annonce) {
                //$scope.selected = selectedItem;
                self.annonces.append(annonce);
            });
        };


        this.edit = function(annonce) {
            openModal(annonce);
        };

        this.add = function() {
            openModal({});
        };
    };

    var modalController = function($modalInstance, AnnoncesService, annonce) {
        this.annonce = annonce;
        this.annonce.create_date = Date.parse(this.annonce.create_date, 'yyyy-mm-dd');
        this.annonce.end_date = Date.parse(this.annonce.end_date, 'yyyy-mm-dd');

        this.ok = function () {
            $modalInstance.close(this.annonce);
        };

        this.cancel = function () {
            $modalInstance.dismiss('cancel');
        };
    };

    var annoncesService = function($http) {
        this.all = function() {
            return $http({url:BASE_PATH + '/admin/annonces/list'});
        };

        this.save = function(annonce) {

        };

    };

    angular
        .module('jdRoll.AdminApp', ['ui.bootstrap'])
        .controller('AnnoncesController', annoncesController)
        .controller('ModalAnnonceController', modalController)
        .service('AnnoncesService', annoncesService);
})();