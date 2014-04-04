angular.module("jdRoll.controller.authentification", ["jdRoll.service.session"]).
controller('AuthenticationController',function($scope,$location,SessionService) {

		$scope.logout=function(){		
			SessionService.logout();
		};

        $scope.authenticateUser= function(){
			SessionService.login($scope.login, $scope.password).
			then(function(data) {
				$location.path('/');
			}).catch(function() {
				$scope.message = "Login ou mot de passe incorrect";
			});
		};
		
		$scope.resetUserPassword= function(){
		/*
			var postData = {};
			postData.login = $scope.login;
			UserService.resetUserPassword(postData,function(data){

				$rootScope.errorMessage = data.message;
				$rootScope.errorLevel = data.level;
				
			},function(data){

				$rootScope.errorLevel = data.data.level;
				$rootScope.errorMessage = data.data.message;
				
			});*/
		};
});