var current_user = {};

angular.module('pickCoolApp', ['ezfb'])
.config(function (ezfbProvider) {
  ezfbProvider.setInitParams({
   appId: '1497159643900204',
   version   : 'v2.2'
  });  
})
.controller('MainCtrl', function ($scope, $http, ezfb, $window, $location) {
  updateLoginStatus(updateApiMe);

  $scope.login = function () {
   ezfb.login(function (res) {
    if (res.authResponse) {
      updateLoginStatus(updateApiMe);
    }
   }, {
    scope: 'public_profile,email,user_likes',
    default_audience: 'everyone',
   });
  };

  $scope.logout = function () {
   ezfb.logout(function () {
    updateLoginStatus(updateApiMe);
   });
  };

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
    if($scope.is_user_logged_in)
    {
      $('.candidate').removeClass('selected');
      $('#c_'+c.id).addClass('selected');
      $('#vote_step_1').modal();
      $scope.current_selection = c;
      $('#vote_step_1 .action-vote').animate( {
        width: '20%',
      },1000,null,function() {
        $('#vote_step_1 .action-share').animate( {
          width: '40%',
        },1000);
      });
    } else {
     $('#login_dialog').modal();
    }
    console.log(c);
  };

  /**
   * Update loginStatus result
   */
  function updateLoginStatus (more) {
    ezfb.getLoginStatus(function (res) {
     $scope.loginStatus = res;
     (more || angular.noop)();
    });
  }

  /**
   * Update api('/me') result
   */
  function updateApiMe () {
    ezfb.api('/me', function (res) {
     $scope.currentUser = res;
    });
  }
  
  $scope.fb_loaded = false;
  $scope.is_user_logged_in = false;
  $scope.$watch('loginStatus', function(newVal,oldVal,scope) {
    $scope.fb_loaded = newVal != undefined;
    $scope.is_user_logged_in = newVal && newVal.status == 'connected';
    if($scope.is_user_logged_in) {
      $('#login_dialog').modal('hide');
    }
  });
  $scope.candidates = [];
  $http.get(API_ENDPOINT+'/contests/featured').success(function($data) {
   $scope.candidates = $data.data;
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
