
app.controller('HotCtrl', function(api, $scope) {
  api.getContests('hot', function(res) {
    $scope.contests = res.data;
  });
});