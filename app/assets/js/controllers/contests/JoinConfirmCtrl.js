console.log('JoinConfirmCtrl.js loaded');
app.controller('JoinConfirmCtrl', function($state, ezfb, $scope, $stateParams, api, $location, $filter, $anchorScroll, $timeout, $cookieStore) {
  console.log('JoinConfirmCtrl');

  $scope.contest = $scope.contests_by_id[$stateParams.contest_id];

  $scope.joined = false;
  api.joinContest($scope.contest.id, function(res) {
    $scope.contest = res.data;
    angular.forEach($scope.contests, function(c,idx) {
      if(c.id != $scope.contest.id) return;
      $scope.contests[idx] = $scope.contest;
      $scope.contests_by_id[$scope.contest.id] = $scope.contest;
      $scope.contest.has_joined = true;
    })
    var candidate = $scope.contest.current_user_writein;
    api.vote(candidate.id, function(res)
    {
      $scope.contest = res.data;
      angular.forEach($scope.contests, function(c,idx) {
        if(c.id != $scope.contest.id) return;
        $scope.contests[idx] = $scope.contest;
        $scope.contests_by_id[$scope.contest.id] = $scope.contest;
        $scope.contest.has_joined = true;
      });
    });
    ezfb.ui(
     {
      method: 'share',
      href: candidate.canonical_url,
     },
     function (res) {
     }
    );    
    $scope.joined = true;
  });
});