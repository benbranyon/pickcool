app.controller('MainCtrl', function ($state, $scope, $window, $location, api) {
  console.log('MainCtrl');
  $scope.state = $state;
  
  if($scope.fb_loaded)
  {
    $scope.$broadcast('go');
  }
  
  $scope.$watch('current_user', function(newVal,oldVal,scope) {
    if($scope.current_user) {
      $('#login_dialog').modal('hide');
    }
  });
  
  $scope.lazyload = function(event, inview, src) {
    var $e = angular.element(event.inViewTarget);
    $e.attr('src', src);
    console.log(src);
  };

  $scope.candidates = [];
});
