/**
 * Controller to create a carte
 */
ngApplication.controller('CtrlCarteCreator', ['$scope', '$http', '$location',
function ($scope, $http, $location) {
    $scope.carte = {
        campagne_id: $scope.getCampagneId(),
        published: '1',
        markers: []
    }

    $scope.messages = [];

    $scope.create = function(){
        $scope.messages = [];
        if( ! $scope.carte.name){
            $scope.messages.push('Le nom est obligatoire.')
        }
        if( ! $scope.carte.image){
            $scope.messages.push('L\'image est obligatoire.')
        }
        if( ! $scope.messages.length){
            $http.post('carte/save', $scope.carte).success(function(carte){
                $location.path('/carte/' + carte.id)
            });
        }
    }
}]);
