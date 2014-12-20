app.directive('scrollTo', function($timeout, $anchorScroll) {
  return function(scope, element, attrs) {
    $timeout(function() {
      var $e = $('.candidate .thumb');
      $e.height($e.width());
      $anchorScroll();
    });
  };
});
app.controller('ContestViewCtrl', function($state, ezfb, $scope, $stateParams, api, $location, $filter, $anchorScroll) {
  console.log('ContestViewCtrl');
  $anchorScroll.yOffset = 50;
   
  api.getContest($stateParams.contest_id, function(res) {
    $scope.contest = res.data;
  });
  
  $scope.join = function() {
    if(!$scope.current_user)
    {
      angular.element('#login_dialog').modal();
      return;
    }
    angular.element('#join').modal('show');
    ezfb.api('/me/picture', {width: 1200, height: 1200}, function (res) {
      $scope.current_user.profile_img_url = res.data.url;
    });
  };
  
  $scope.join_confirm = function() {
    angular.element('#join').modal('hide');
    angular.element('#join_confirm').modal('show');
    $scope.joined = false;
    api.joinContest($scope.contest.id, function(res) {
      $scope.contest = res.data;
      $scope.joined = true;
    });
  };

  $scope.unvote = function(c) {
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
    if(!$scope.current_user)
    {
      angular.element('#login_dialog').modal();
      return;
    }

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
});