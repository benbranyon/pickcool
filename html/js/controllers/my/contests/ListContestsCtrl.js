app.controller('ListContestsCtrl', function ($scope, $http, $state, api) {
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