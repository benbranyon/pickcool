
app.controller('ContestViewCtrl', function(ezfb, $scope, $stateParams, api) {
  api.getContest($stateParams.id, function(res) {
    $scope.contest = res.data;
  });
  
  $scope.share = function () {
    ezfb.ui(
     {
      method: 'feed',
      name: 'angular-easyfb API demo',
      picture: 'http://v2.7-beta.clipbucket.com/files/photos/2014/05/16/1400228168b6b742_l.jpg',
      link: 'http://pick.cool/vote?/qclqht?p=preview',
      description: 'Taylor Siwft is WAAAY better than Justin Beiber! Help me prove it by voting and sharing.'
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
      $('.candidate').removeClass('selected');
      $('#c_'+c.id).addClass('selected');
      if($scope.contest.current_user_candidate_id)
      {
        angular.forEach($scope.contest.candidates, function(v,k) {
          if($scope.contest.current_user_candidate_id != v.id) return;
          v.vote_count--;
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