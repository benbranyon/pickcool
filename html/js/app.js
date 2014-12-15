var current_user = {};

var app = angular.module('pickCoolApp', ['ezfb', 'ui.router', 'ng', 'ngFlash'])
.config(function (ezfbProvider) {
  ezfbProvider.setInitParams({
    appId: '1497159643900204',
    version   : 'v2.2',
    status: true,
  });  
})
.config(function($flashProvider) {
  $flashProvider.setRouteChangeSuccess('$stateChangeSuccess');
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
.run(function(ezfb,$rootScope,$http,api,$templateCache) {
  $templateCache.put('template/flash-messages.html', 
        '<div class="flash-messages">' +
          '<div class="flash-message alert alert-{{message.type}}" ng-repeat="message in _flash.messages">' +
            '<a href="" class="close" ng-click="message.remove()"></a>' +
            '<span class="flash-content" ng-bind-html="message.message"></span>' +
          '</div>' +
        '</div>'
    );

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
    $rootScope.session_started = false;
    if(!res.authResponse) 
    {
      $rootScope.current_user = null;
      console.log('Unauthenticated');
      $rootScope.session_started = true;
      return;
    }
    $rootScope.accessToken = res.authResponse.accessToken;
    api.getUser(function(res) {
      if(res.status=='ok')
      {
        $rootScope.current_user = res.data;
        console.log('Authenticated');
        $rootScope.session_started = true;
      } else {
        console.log("API Error");
      }
    });
  }
  
  ezfb.getLoginStatus(updateStatus);
  
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
   window.location = '/';
  };
});
