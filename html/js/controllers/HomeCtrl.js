
app.controller('HomeCtrl', function($http, $scope) {
  $http.get(API_ENDPOINT+'/contests/featured').success(function(res) {
   $scope.contests = res.data;
  });
});