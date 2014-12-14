app.config(function($stateProvider, $urlRouterProvider, $locationProvider) {
  $locationProvider.html5Mode(true).hashPrefix('!');
  
  // For any unmatched url, redirect to /state1
  $urlRouterProvider.otherwise("/");
  //
  // Now set up the states
  $stateProvider
    .state('home', {
      url: "/",
      templateUrl: "partials/list.html",
      controller: function(api, $scope) {
        api.getContests('hot', function(res) {
          $scope.contests = res.data;
        });
      },
    })
    .state('contests-view', {
      url: "/est/:contest_id/:slug",
      templateUrl: "partials/contests/view.html"
    })
    .state('contests-share', {
      url: "/est/:contest_id/:slug/:user_id/:candidate_id",
      templateUrl: "partials/contests/view.html"
    })
    .state('image-view', {
      url: '/images/:id/:size'
    })
    .state('buy', {
      url: '/shop/:candidate_id',
      'template': function() {
        return 'Thanks for supporting pick.cool!';
      },
      controller: function($location) {
        window.location.replace($location.url());
      }
    })
    .state('hot', {
      url: "/hot",
      templateUrl: "partials/list.html",
      controller: function(api, $scope) {
        api.getContests('hot', function(res) {
          $scope.contests = res.data;
        });
      },
    })
    .state('new', {
      url: "/new",
      templateUrl: "partials/list.html",
      controller: function(api, $scope) {
        api.getContests('new', function(res) {
          $scope.contests = res.data;
        });
      },
    })
    .state('top', {
      url: "/top",
      templateUrl: "partials/list.html",
      controller: function(api, $scope) {
        api.getContests('top', function(res) {
          $scope.contests = res.data;
        });
      },
    })
    .state('contests-create', {
      url: "/contests/create",
      templateUrl: "partials/contests/create.html"
    })
    .state('contests-edit', {
      url: "/contests/:contest_id/edit",
      templateUrl: "partials/contests/edit.html"
    })
  ;
})