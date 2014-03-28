
app.controller('MenuController',function($rootScope,$http,$scope,$location,SessionService,UserService) {
 
		  $scope.showHideAuthForm=function(){
		
			return !SessionService.isLogged;
		 }
		 
		 $scope.getCurrentUserName=function(){
		 
			return SessionService.username;
		 }
     
});