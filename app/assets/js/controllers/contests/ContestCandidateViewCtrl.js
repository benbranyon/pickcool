console.log('ContestCandidateViewCtrl.js loaded');
app.controller('ContestCandidateViewCtrl', function($state, ezfb, $scope, $stateParams, api, $location, $filter, $anchorScroll, $timeout, $cookieStore) {
  console.log('ContestCandidateViewCtrl');

  $scope.contest = $scope.contests_by_id[$stateParams.contest_id];
  $scope.candidate = $scope.contest.candidates_by_id[$stateParams.candidate_id];

  $scope.vote = function() {
    if(!$scope.contest.can_vote) return; 
    if(!$scope.current_user)
    {
      $state.go('login', {r: $location.href()});
      return;
    }
    var c = $scope.candidate;

    if(c.id == $scope.contest.current_user_candidate_id) return;
    angular.element('.candidate').removeClass('selected');
    angular.element('#c_'+c.id).addClass('selected');
    if($scope.contest.current_user_candidate_id )
    {
      angular.forEach($scope.contest.candidates, function(c,k) {
        if($scope.contest.current_user_candidate_id != c.id) return;
        c.vote_count--;
      });
    }
    c.vote_count++;
    $scope.contest.current_user_candidate = c;
    $scope.contest.current_user_candidate_id = c.id;
    api.vote(c.id);
  };
  
  $scope.unvote = function() {
    if(!$scope.contest.can_vote) return; 
    var c = $scope.candidate;
    angular.element('.candidate').removeClass('selected');
    c.vote_count--;
    $scope.contest.current_user_candidate = null;
    $scope.contest.current_user_candidate_id = null;
    api.unvote(c.id);
  };
  
  $scope.scroll = function() {
    $anchorScroll();
  }
   
  $scope.share = function (c) {
    ezfb.ui({
      method: 'share',
      href: $scope.contest.canonical_url,
    });    
  };
  

});