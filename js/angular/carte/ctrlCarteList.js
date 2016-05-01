/**
 * Controller to create a carte
 */
ngApplication.controller('CtrlCarteList', ['$scope', '$http', '$location', 'growl', 'cartes',
function ($scope, $http, $location, growl, cartes) {
    $scope.cartes = cartes;

    $scope.create = function(){
        $location.path('/carte/create');
    }
    $scope.open = function(carte){
        $location.path('/carte/' + carte.id);
    }
    $scope.delete = function(carte){
        bootbox.confirm("Supprimer cette carte ?", function(confirmed) {
            if(confirmed) {
                $http.get('carte/delete/' + carte.id).then(function(){
                    $scope.cartes.splice($scope.cartes.indexOf(carte), 1);
                    growl.success('Carte supprimée.');
                })
            }
        });
    }
}]);