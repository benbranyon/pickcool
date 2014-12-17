
app.controller('ContestViewCtrl', function($state, ezfb, $scope, $stateParams, api, $location, $filter) {
  console.log('ContestViewCtrl');
  api.getContest($stateParams.contest_id, function(res) {
    $scope.contest = res.data;
  });
  
  $scope.discuss = function(candidate) {
    $scope.discuss_candidate = candidate;
    $('#discuss').modal('show');
  };
  
  $scope.share = function (c) {
    var url = c.canonical_url;
    ezfb.ui(
     {
      method: 'share',
      href: url,
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
        });
      }
      c.vote_count++
      $scope.contest.current_user_candidate = c;
      $scope.contest.current_user_candidate_id = c.id;
      api.vote(c.id);
    } else {
     $('#login_dialog').modal();
    }
    console.log(c);
  };
});