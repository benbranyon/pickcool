console.log('ContestViewCtrl.js loaded');
app.directive('scrollTo', function($timeout, $anchorScroll) {
  return function(scope, element, attrs) {
    $timeout(function() {
      console.log('sizing');
      var $e = $('.candidate .thumb');
      $e.height($e.width());
      $anchorScroll();
    });
  };
});
app.controller('ContestViewCtrl', function($state, ezfb, $scope, $stateParams, api, $location, $filter, $anchorScroll, $timeout, $cookieStore) {
  console.log('ContestViewCtrl');
  $anchorScroll.yOffset = 50;

  $scope.contest = $scope.contests_by_id[$stateParams.contest_id];
  $scope.input = {password: $scope.contest_passwords($scope.contest.id)};
  $scope.$watch('input.password', function() {
    $scope.contest_passwords($scope.contest.id, $scope.input.password);
  });

  $scope.join = function() {
    if(!$scope.contest.can_join) return; 
    if($scope.contest.password && $scope.contest.password != $scope.input.password) return;
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
    if(!$scope.contest.can_join) return; 
    if($scope.contest.password && $scope.contest.password != $scope.input.password) return;
    angular.element('#join').modal('hide');
    angular.element('#join_confirm').modal('show');
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

  $scope.unvote = function(c) {
    if(!$scope.contest.can_vote) return; 
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
    if(!$scope.contest.can_vote) return; 

    
    return;
    var serialize = function(obj) {
      var str = [];
      for(var p in obj)
        if (obj.hasOwnProperty(p)) {
          str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        }
      return str.join("&");
    };
    qs = {
      app_id: '1497159643900204',
      display: 'page',
      href: c.canonical_url,
      redirect_uri: $location.absUrl(),
    };
    window.location = "https://www.facebook.com/dialog/share?"+serialize(qs);
    return;
        
  };
  
  $scope.vote = function(c) {
    if(!$scope.contest.can_vote) return; 
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