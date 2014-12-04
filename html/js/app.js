var current_user = {};

angular.module('pickCoolApp', ['ezfb', 'ui.router'])
.config(function (ezfbProvider) {
  ezfbProvider.setInitParams({
   appId: '1497159643900204',
   version   : 'v2.2'
  });  
})
.config(function($stateProvider, $urlRouterProvider) {
  //
  // For any unmatched url, redirect to /state1
  $urlRouterProvider.otherwise("/");
  //
  // Now set up the states
  $stateProvider
    .state('home', {
      url: "/",
      templateUrl: "partials/vote.html"
    })
    .state('my-contests', {
      url: "/my/contests",
      templateUrl: "partials/my/contests/list.html"
    })
    .state('my-contests-create', {
      url: "/my/contests/create",
      templateUrl: "partials/my/contests/create.html"
    })
  ;
})
.controller('MainCtrl', function ($scope, $http, ezfb, $window, $location) {
  updateLoginStatus();

  $scope.current_user = null;
  $scope.login = function () {
   ezfb.login(function (res) {
    if (res.authResponse) {
      updateLoginStatus();
    }
   }, {
    scope: 'public_profile,email,user_likes',
    default_audience: 'everyone',
   });
  };

  $scope.logout = function () {
   ezfb.logout(function () {
    updateLoginStatus();
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
    if($scope.current_user)
    {
      $('.candidate').removeClass('selected');
      $('#c_'+c.id).addClass('selected');
      $('#vote_step_1').modal();
      $scope.current_selection = c;
      $http.get(API_ENDPOINT+'/vote', 
        {
          'params': {
            'accessToken': $scope.loginStatus.authResponse.accessToken,
            'c': c.id
          }
        }
      )
        .success(function(data) {
          console.log(data);
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
     $scope.current_user = null;
     if(!$scope.loginStatus.authResponse) 
     {
       $scope.fb_loaded = true;
       return;
     };
     $http.get(API_ENDPOINT+'/user', 
       {
         'params': {
           'accessToken': $scope.loginStatus.authResponse.accessToken,
         }
       }
     ).success(function(res) {
       $scope.fb_loaded = true;
       if(res.status=='ok')
       {
         $scope.current_user = res.data;
       }
     });
    });
  }
  
  $scope.fb_loaded = false;
  $scope.is_user_logged_in = false;
  $scope.$watch('current_user', function(newVal,oldVal,scope) {
    if($scope.current_user) {
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
