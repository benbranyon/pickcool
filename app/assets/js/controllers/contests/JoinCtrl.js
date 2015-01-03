console.log('JoinCtrl.js loaded');
app.controller('JoinCtrl', function($state, ezfb, $scope, $stateParams, api, $location, $filter, $anchorScroll, $timeout, $cookieStore) {
  console.log('JoinCtrl');

  $scope.contest = $scope.contests_by_id[$stateParams.contest_id];

  if(!$scope.current_user)
  {
    $state.go('login', {r: $location.href()});
    return;
  }
  ezfb.api('/me/picture', {width: 1200, height: 1200}, function (res) {
    $scope.current_user.profile_img_url = res.data.url;
  });
  
  $scope.join_confirm = function() {
    $scope.joined = false;
    api.joinContest($scope.contest.id, function(res) {
      $scope.contest = res.data;
      angular.forEach($scope.contests, function(c,idx) {
        if(c.id != $scope.contest.id) return;
        console.log('found it');
        $scope.contests[idx] = $scope.contest;
        $scope.contests_by_id[$scope.contest.id] = $scope.contest;
        $scope.contest.has_joined = true;
      })
      $scope.joined = true;
    });
  };
});