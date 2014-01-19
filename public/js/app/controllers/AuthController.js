
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
		
			var dataRes = JSON.stringify(data)
			
             if(data[0].id){
                 $rootScope.message = "";
				 SessionService.isLogged = true;
				 SessionService.username = data[0].username;
				 if(data[0].profil == 0)
					SessionService.isAdmin = true;
				else
					SessionService.isAdmin = false;
                 $location.path('/');
             }
             else{
                 $rootScope.message = "Login ou mot de passe incorrect";
             }
                
         });
    }
     
});