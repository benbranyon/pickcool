app.controller('ListContestsCtrl', function ($scope, $http, $state) {
  if(!$scope.fb_loaded)
  {
    $state.go('home');
    return; 
  }
  console.log($scope.loginStatus);
  $scope.contests = [];
  $http.get(API_ENDPOINT+'/my/contests', 
    {
      'params': {
        'accessToken': $scope.loginStatus.authResponse.accessToken,
        'candidates': JSON.stringify($scope.candidates)
      }
    }
  )
  .success(function(res) {
    $scope.contests = res.data;
  });
});