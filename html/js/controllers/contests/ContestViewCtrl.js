
app.controller('ContestViewCtrl', function($http, $scope, $stateParams) {
  $http.get(API_ENDPOINT+'/contests/'+$stateParams.id).success(function(res) {
   $scope.contest = res.data;
  });
});