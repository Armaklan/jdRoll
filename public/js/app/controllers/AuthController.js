
app.controller('AuthenticationController',function($rootScope,$http,$scope,$location,SessionService,UserService) {
 
		 $scope.ShowHideForm=function(){
		
			return !SessionService.isLogged;
		 }
		 
		  $scope.getUserName=function(){
		 
			return SessionService.username;
		 }

		 $scope.logout=function()
		 {		
			 SessionService.username ='';
			 SessionService.isLogged = false;
			 SessionService.isAdmin = false;
			 UserService.delete({obj:'session'},function(){
			 $location.path('/');
		 });
		  
		 
		 }
         $scope.authenticate= function(){
		
         var postData = {};
          postData.login = $scope.login;
          postData.password = $scope.password;
		 $scope.login = '';
		 $scope.password = '';
		 
		 UserService.authenticate(postData,function(data){
		 
                 $rootScope.message = "";
				 SessionService.isLogged = true;
				 SessionService.username = data.username;
				 if(data.profil == 0)
					SessionService.isAdmin = true;
				else
					SessionService.isAdmin = false;
                 $location.path('/');
                
         },function()
		 {
			$rootScope.message = "Login ou mot de passe incorrect";
		 });
    }
     
});