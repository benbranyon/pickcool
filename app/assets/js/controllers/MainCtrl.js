app.controller('MainCtrl', function ($state, $scope, $window, $location, api, $anchorScroll, $timeout) {
  console.log('MainCtrl');
  $scope.state = $state;
  
  $scope.contests=null;

  $scope.$watch('session_started', function() {
    if(!$scope.session_started) return;
    var refresh = function() {
      api.getContests('local', function(res) {
        $scope.contests = res.data;
        $scope.contests_by_id = {};
        angular.forEach($scope.contests, function(c) {
          $scope.contests_by_id[c.id]=c;
        });
      });
      $timeout(refresh, 60 * 1000);
    };
    refresh();
  });
  
  
  $scope.scrollTop = function() {
    $location.hash('top');
    $anchorScroll();
  };
  
  $scope.$watch('current_user', function(newVal,oldVal,scope) {
    if($scope.current_user) {
      angular.element('#login_dialog').modal('hide');
    }
  });
  
  $scope.lazyload = function(event, inview, src) {
    var $e = angular.element(event.inViewTarget);
    $e.attr('src', src);
    console.log(src);
  };

  $scope.candidates = [];
});
app.run(function($cookieStore, $rootScope) {
  $rootScope.contest_passwords = function(id) {
    if(!$cookieStore.get('contest_passwords'))
    {
      $cookieStore.put('contest_passwords', {});
    }
    var pw = $cookieStore.get('contest_passwords');
    if(arguments.length>1) {
      pw[id] = arguments[1];
      $cookieStore.put('contest_passwords', pw);
    }
    return pw[id];
  };
});
