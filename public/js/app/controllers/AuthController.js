
app.controller('AuthenticationController',function($rootScope,$http,$scope,$location,SessionService,UserService) {

		 $scope.logout=function()
		 {		
			 SessionService.username ='';
			 SessionService.isLogged = false;
			 SessionService.isAdmin = false;
			 UserService.deleteUserSession(function(){
				$location.path('/');
			 });
			  
		 
		 }
		 
         $scope.authenticateUser= function(){
		
			var postData = {};
			postData.login = $scope.login;
			postData.password = $scope.password;
			$scope.login = '';
			$scope.password = '';
		 
			UserService.authenticateUser(postData,function(data){
		 
				$rootScope.message = "";
				SessionService.isLogged = true;
				SessionService.username = data.username;
				SessionService.isAdmin = data.profil == 0 ? true : false;
                $location.path('/');
                
			},function(){
				$rootScope.message = "Login ou mot de passe incorrect";
			});
		}
});