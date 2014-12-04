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
.controller('CreateContestCtrl', function ($scope, $http, $state) {
  $scope.candidates = [
    {'amazon_url': 'http://www.amazon.com/s/?_encoding=UTF8&camp=1789&creative=390957&field-keywords=taylor%20swift&linkCode=ur2&tag=benallfree-20&url=search-alias%3Daps&linkId=TRUKIE3Z25IK2IHK', "image_url":"http:\/\/v2.7-beta.clipbucket.com\/files\/photos\/2014\/05\/16\/1400228168b6b742_l.jpg","vote_count":747,"id":1,"name":"Taylor Swift"},
    {'amazon_url': 'http://www.amazon.com/s/?_encoding=UTF8&camp=1789&creative=390957&field-keywords=justin%20bieber&linkCode=ur2&rh=i%3Aaps%2Ck%3Ajustin%20bieber&sprefix=justin%20bi%2Caps%2C429&tag=benallfree-20&url=search-alias%3Daps&linkId=WDZIWQQER6STSOST', "image_url":"http:\/\/assets-s3.usmagazine.com\/uploads\/assets\/articles\/74065-justin-bieber-apologizes-after-offensive-n-word-video-surfaces\/1401970999_justin-bieber-lg.jpg","vote_count":223,"id":2,"name":"Justin Beiber"},
    {'amazon_url': 'http://www.amazon.com/s/?_encoding=UTF8&ajr=0&camp=1789&creative=390957&field-keywords=britney%20spears&linkCode=ur2&rh=i%3Aaps%2Ck%3Abritney%20spears&sprefix=britney%20spears%2Cdigital-music%2C199&tag=benallfree-20&url=search-alias%3Daps&linkId=OIPNX5Y3XFXFRV3O', "image_url":"https:\/\/pbs.twimg.com\/profile_images\/426108979186384896\/J3JDXvs4_400x400.jpeg","vote_count":463,"id":3,"name":"Britney Spears"},
    {'amazon_url': 'http://www.amazon.com/s/?_encoding=UTF8&camp=1789&creative=390957&field-keywords=justin%20timberlake&linkCode=ur2&sprefix=justin%20tim%2Cdigital-music%2C216&tag=benallfree-20&url=search-alias%3Ddigital-music&linkId=AEGQJMRSWKLBHEAD', "image_url":"http:\/\/www.billboard.com\/files\/media\/justin-timberlake-2013-suite-tie-650-430.jpg","vote_count":993,"id":4,"name":"Justin Timberlake"}
  ];
  
  $scope.add = function() {
    $scope.candidates.push({'id': $scope.candidates.length+1});
    console.log($scope.candidates);
  };
  
  $scope.save = function() {
    $http.get(API_ENDPOINT+'/my/contests/create', 
      {
        'params': {
          'accessToken': $scope.loginStatus.authResponse.accessToken,
          'candidates': JSON.stringify($scope.candidates)
        }
      }
    )
    .success(function(data) {
      if(data.error_message)
      {
        for(var i in data.error_message)
        {
          var error_bag = data.error_message[i];
          $scope.candidates[error_bag.id-1].error_message = error_bag.messages;
        }
      } else {
        $state.go('my-contests');
      }
    });

    console.log('save');
  }
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
