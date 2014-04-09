/**
 * Created by zuberl on 04/04/2014.
 */

angular.module('jdRoll.controller.users', ['jdRoll.service.user']).
controller('UserController', function($location, $rootScope, $scope, User){

    $rootScope.campaignSpace = false;

    $scope.users = User.query({});

    $scope.showUser = function(user) {
    	$location.path("#/user/" + user.username);
    }
});
