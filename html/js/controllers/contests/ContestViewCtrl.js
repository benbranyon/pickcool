
app.controller('ContestViewCtrl', function(ezfb, $scope, $stateParams, api, $filter) {
  console.log('ContestViewCtrl');
  console.log($stateParams.contest_id);
  api.getContest($stateParams.contest_id, function(res) {
    $scope.contest = res.data;
    // Fix up contest data
    $scope.contest.highest_vote = 0;
    $scope.contest.total_votes = 0;
    angular.forEach($scope.contest.candidates, function(c,idx) {
      if(c.vote_count > $scope.contest.highest_vote) $scope.contest.highest_vote = c.vote_count;
      $scope.contest.total_votes = $scope.contest.total_votes + c.vote_count;
    });
    $scope.contest.candidates = $filter('orderBy')($scope.contest.candidates, 'vote_count', true);
  });
  
  $scope.share = function () {
    ezfb.ui(
     {
      method: 'share',
      href: $scope.contest.canonical_url,
     },
     function (res) {
      console.log(res);
      // res: FB.ui response
     }
    );        
  };
  
  $scope.vote = function(c) {
    if($scope.current_user)
    {
      if(c.id == $scope.contest.current_user_candidate_id) return;
      $('.candidate').removeClass('selected');
      $('#c_'+c.id).addClass('selected');
      if($scope.contest.current_user_candidate_id )
      {
        angular.forEach($scope.contest.candidates, function(c,k) {
          if($scope.contest.current_user_candidate_id != c.id) return;
          c.vote_count--;
          $scope.updateVoteProgress($scope.contest, c);
        });
      }
      c.vote_count++
      $scope.contest.current_user_candidate_id = c.id;
      $scope.updateVoteProgress($scope.contest, c);
      api.vote(c.id);
    } else {
     $('#login_dialog').modal();
    }
    console.log(c);
  };
});