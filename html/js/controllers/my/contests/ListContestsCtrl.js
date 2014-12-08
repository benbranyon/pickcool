app.controller('ListContestsCtrl', function ($scope, $state, api) {
  if(!$scope.current_user)
  {
    $state.go('home');
    return; 
  }
  $scope.contests = [];
  api.getMyContests(function(res) {
    $scope.contests = res.data;
  });
});