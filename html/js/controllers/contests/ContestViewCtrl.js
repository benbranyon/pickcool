
app.controller('ContestViewCtrl', function($scope, $stateParams, api) {
  api.getContest($stateParams.id, function(res) {
    $scope.contest = res.data;
  });
});