
app.controller('ContestViewCtrl', function($state, ezfb, $scope, $stateParams, api, $location, $filter) {
  console.log('ContestViewCtrl');
  api.getContest($stateParams.contest_id, function(res) {
    $scope.contest = res.data;
  });
  
  $scope.share = function () {
    var share = function(response) { 
      console.log(response);
      ezfb.ui(
       {
        method: 'share',
        href: $scope.contest.share_url($scope.contest.current_user_candidate_id),
       },
       function (res) {
        console.log(res);
        // res: FB.ui response
       }
      );
    };
    if(DEBUG)
    {
      $.post('https://graph.facebook.com', {'id': url, 'scrape': true}, share);
    } else {
      share();
    }
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