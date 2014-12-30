app.controller('MainCtrl', function ($state, $scope, $window, $location, api, $anchorScroll) {
  console.log('MainCtrl');
  $scope.state = $state;
  
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
