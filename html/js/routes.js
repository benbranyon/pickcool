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
      controller: 'HomeCtrl',
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
      controller: 'HomeCtrl',
    })
    .state('new', {
      url: "/new",
      templateUrl: "partials/list.html",
      controller: 'HomeCtrl',
    })
    .state('top', {
      url: "/top",
      templateUrl: "partials/list.html",
      controller: 'HomeCtrl',
    })
    .state('ended', {
      url: "/ended",
      templateUrl: "partials/list.html",
      controller: 'HomeCtrl',
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