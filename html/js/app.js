var current_user = {};

var app = angular.module('pickCoolApp', ['ezfb', 'ui.router'])
.config(function (ezfbProvider) {
  ezfbProvider.setInitParams({
    appId: '1497159643900204',
    version   : 'v2.2',
    status: true,
  });  
})
.directive('initCandidate', function() {
  return function(scope, element, attrs) {
   var c = scope.c;
   var $e = $(element).find('.mProgress1');
   $e.animate({
    height: ((c.vote_count/scope.contest.max_votes)*100.0)+'%'
    }, 1000);
   $e.css('background-color', '#'+rainbow.colorAt(c.vote_pct*1000.0));
  };
})
.directive('ngLadda', function() {
  return function(scope, element, attrs) {
    Ladda.bind(element[0]);
  };
})
.run(function(ezfb,$rootScope,$http) {
  $rootScope.current_user = null;
  $rootScope.accessToken = null;

  function updateStatus(res) 
  {
    console.log("auth.statusChange",res);
    $rootScope.fb_loaded = true;
    $rootScope.accssToken = null;
    if(!res.authResponse) 
    {
      $rootScope.current_user = null;
      return;
    }
    $rootScope.accessToken = res.authResponse.accessToken;
    $http.get(API_ENDPOINT+'/user', 
      {
        'params': {
          'accessToken': $rootScope.accessToken,
        }
      }
    ).success(function(res) {
      if(res.status=='ok')
      {
        $rootScope.current_user = res.data;
      }
    });
  }
  ezfb.Event.subscribe('auth.statusChange', updateStatus);
//  ezfb.getLoginStatus().then(updateStatus);
  
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
