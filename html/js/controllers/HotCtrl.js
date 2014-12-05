
app.controller('HotCtrl', function($http, $scope) {
  $http.get(API_ENDPOINT+'/contests/hot').success(function(res) {
   $scope.contests = res.data;
  });
});