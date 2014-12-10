
app.controller('ContestViewCtrl', function(ezfb, $scope, $stateParams, api) {
  console.log('ContestViewCtrl');
  console.log($stateParams.contest_id);
  api.getContest($stateParams.contest_id, function(res) {
    $scope.contest = res.data;
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
        });
      }
      if($scope.contest.current_user_candidate_id != c.id) c.vote_count++
      api.vote(c.id, function(res) {
        angular.extend($scope.contest, res.data);
        angular.forEach($scope.contest.candidates, function(c,k) {
          var $e = $('#candidate_'+c.id).find('.mProgress1');
          $e.animate({
           height: ((c.vote_count/$scope.contest.max_votes)*100.0)+'%'
           }, 1000);
          $e.css('background-color', '#'+rainbow.colorAt(c.vote_pct*1000.0));
        });
      });
    } else {
     $('#login_dialog').modal();
    }
    console.log(c);
  };
});