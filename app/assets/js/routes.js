console.log('routes.js loaded');

app.config(function($stateProvider, $urlRouterProvider, $locationProvider) {
  $locationProvider.html5Mode(true).hashPrefix('!');
  
  // For any unmatched url, redirect to /state1
  $urlRouterProvider.otherwise("/");
  
  // Now set up the states
  $stateProvider
    .state('home', {
      url: "/",
      templateUrl: "partials/list.html",
      controller: function(api, $scope) {
        console.log('HomeController');
        $scope.contests = $scope.contests.sort(function(a,b) {
          return b.vote_count_hot - a.vote_count_hot || b.vote_count - a.vote_count || b.created_at - a.created_at;
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
    .state('contests-share-2', {
      url: "/est/:contest_id/:contest_slug/picks/:candidate_id/:candidate_slug",
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
        console.log('HotController');
        $scope.contests = $scope.contests.sort(function(a,b) {
          return b.vote_count_hot - a.vote_count_hot || b.vote_count - a.vote_count || b.created_at - a.created_at;
        });
      },
    })
    .state('new', {
      url: "/new",
      templateUrl: "partials/list.html",
      controller: function(api, $scope) {
        console.log('RecentController');
        $scope.contests = $scope.contests.sort(function(a,b) {
          console.log(a,b);
          return b.created_at - a.created_at || b.vote_count - a.vote_count;
        });
      },
    })
    .state('top', {
      url: "/top",
      templateUrl: "partials/list.html",
      controller: function(api, $scope) {
        console.log('TopController');
        $scope.contests = $scope.contests.sort(function(a,b) {
          console.log(b.vote_count - a.vote_count);
          return b.vote_count - a.vote_count;
        });
      },
    })
    .state('contests-create', {
      url: "/contests/create",
      controller: 'CreateContestCtrl',
      templateUrl: "partials/contests/edit.html"
    })
    .state('contests-edit', {
      url: "/contests/:contest_id/edit",
      controller: 'EditContestCtrl',
      templateUrl: "partials/contests/edit.html"
    })
    .state('privacy', {
      url: "/privacy",
      templateUrl: "partials/privacy.html"
    })
    .state('/terms', {
      url: "/terms",
      templateUrl: "partials/terms.html"
    })
  ;
})