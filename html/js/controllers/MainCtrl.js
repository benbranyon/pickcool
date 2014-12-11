app.controller('MainCtrl', function ($state, $scope, $window, $location, api) {
  console.log('MainCtrl');
  $scope.state = $state;
  
  $scope.$on('go', function() {
    console.log('go for MAIN');
    $scope.$watch('current_user', function(newVal,oldVal,scope) {
      if($scope.current_user) {
        $('#login_dialog').modal('hide');
      }
    });

  });
  


  $scope.candidates = [];
});

