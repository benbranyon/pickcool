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
    height: ((c.vote_count/1000.0)*100.0)+'%'
    }, 1000);
   $e.css('background-color', '#'+rainbow.colorAt(c.vote_count));
  };
})
.directive('ngLadda', function() {
  return function(scope, element, attrs) {
    Ladda.bind(element[0]);
  };
})
.run(function(ezfb,$rootScope, api) {
  $rootScope.current_user = null;
  $rootScope.accessToken = null;

  $rootScope.updateStatus = function(res) 
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
    api.getUser(function(res) {
      $rootScope.current_user = res.data;
      console.log("Current user is ", $rootScope.current_user);
    });
  }
  ezfb.Event.subscribe('auth.statusChange', $rootScope.updateStatus);
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
