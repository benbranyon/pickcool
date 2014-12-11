var current_user = {};

var app = angular.module('pickCoolApp', ['ezfb', 'ui.router'])
.config(function (ezfbProvider) {
  ezfbProvider.setInitParams({
    appId: '1497159643900204',
    version   : 'v2.2',
    status: true,
  });  
})
.directive('initProgress', function() {
  return function(scope, element, attrs) {
    var $e =  $(element);
    scope.c.$e = $e;
    scope.updateVoteProgress(scope.contest, scope.c);
    
  };
})
.directive('ngLadda', function() {
  return function(scope, element, attrs) {
    Ladda.bind(element[0]);
  };
})
.run(function(ezfb,$rootScope,$http,api) {
  $rootScope.current_user = null;
  $rootScope.accessToken = null;

  $rootScope.updateVoteProgress = function(contest, candidate, $e)
  {
    var c = candidate;
    if(contest.highest_vote == 0) return;
    var scale = 900.0/contest.highest_vote;
    var dc = c.vote_count * scale;
    console.log("Scale is ", scale);
    console.log("Display count is ", dc);
    var $e = c.$e;
    console.log($e.height());
    $e.css('height', (dc/10)+'%');
    $e.css('background-color', '#'+rainbow.colorAt(dc));
  }

  function updateStatus(res) 
  {
    console.log("auth.statusChange",res);
    $rootScope.fb_loaded = true;
    $rootScope.accssToken = null;
    if(!res.authResponse) 
    {
      $rootScope.current_user = null;
      console.log('Unauthenticated');
      $rootScope.$broadcast('go');
      return;
    }
    $rootScope.accessToken = res.authResponse.accessToken;
    api.getUser(function(res) {
      if(res.status=='ok')
      {
        $rootScope.current_user = res.data;
        console.log('Authenticated');
        $rootScope.$broadcast('go');
      } else {
        console.log("API Error");
      }
    });
  }
  
  ezfb.Event.subscribe('auth.statusChange', updateStatus);
  
  ezfb.Event.subscribe('auth.authResponseChanged', function (statusRes) {
    console.log('xx authResponseChanged');
    console.log(statusRes);
  });  

  $rootScope.login = function () {
   ezfb.login(null, {
    scope: 'public_profile,email,user_likes',
    default_audience: 'everyone',
   });
  };

  $rootScope.logout = function () {
   ezfb.logout();
  };
});
