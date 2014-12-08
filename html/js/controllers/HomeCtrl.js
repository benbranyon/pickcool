
app.controller('HomeCtrl', function($scope, api) {
  api.getFeaturedContests(function(res) {
    $scope.contests = res.data;
  });
});