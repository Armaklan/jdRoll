app.directive('jdrollAuthform', function() {
    return {
      templateUrl: 'views/AuthForm.html'
    };
  });
  
app.directive('jdrollAlert', function() {
    return {
        scope: {
            level: '@',
			message: '@'
        },
        templateUrl: 'views/alert.html'
    };
});
 
  