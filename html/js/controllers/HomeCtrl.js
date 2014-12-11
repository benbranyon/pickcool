
app.controller('HomeCtrl', function($scope, api, $location, $state, $filter) {
  console.log('HomeCtrl');
  api.getFeaturedContests(function(res) {
    $scope.contests = res.data;
  });
});