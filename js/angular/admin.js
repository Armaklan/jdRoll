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

            var toMysqlDate = function(jsDate) {
              return jsDate.toISOString().slice(0, 19).replace('T', ' ');
            };

            modalInstance.result.then(function (modalAnnonce) {
                //$scope.selected = selectedItem;
                modalAnnonce.create_date = toMysqlDate(modalAnnonce.create_date);
                modalAnnonce.end_date = toMysqlDate(modalAnnonce.end_date);
                if(modalAnnonce.id) {
                  AnnoncesService.save(modalAnnonce).success(function(data){
                      annonce.create_date = data.create_date;
                      annonce.end_date = data.end_date;
                      annonce.content = data.content;
                      annonce.title = data.title;
                  });
                } else {
                  AnnoncesService.add(modalAnnonce).success(function(data){
                     self.annonces.push(data);
                  });
                }
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

        var toJsDate = function(mysqlDate) {
          return new Date(mysqlDate).toISOString();
        };
        this.annonce = annonce;
        this.annonce.create_date = toJsDate(this.annonce.create_date);
        this.annonce.end_date = toJsDate(this.annonce.end_date);

        this.ok = function () {
            $modalInstance.close(this.annonce);
        };

        this.cancel = function () {
            $modalInstance.dismiss('cancel');
        };

        this.openCreate = function($event) {
          $event.preventDefault();
          $event.stopPropagation();
          this.openedCreate = true;
        };

        this.openEnd = function($event) {
          $event.preventDefault();
          $event.stopPropagation();
          this.openedEnd = true;
        };
    };

    var annoncesService = function($http) {
        this.all = function() {
          return $http({url:BASE_PATH + '/admin/annonces/list'});
        };

        this.save = function(annonce) {
          return $http({
            url:BASE_PATH + '/admin/annonces',
            method: 'PUT',
            data:annonce
          });
        };

        this.add = function(annonce) {
          return $http({
            url:BASE_PATH + '/admin/annonces',
            method: 'POST',
            data:annonce
          });
        };

    };

    angular
        .module('jdRoll.AdminApp', ['ui.bootstrap'])
        .controller('AnnoncesController', annoncesController)
        .controller('ModalAnnonceController', modalController)
        .service('AnnoncesService', annoncesService);
})();


