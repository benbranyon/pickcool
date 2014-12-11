app.config(function($stateProvider, $urlRouterProvider, $locationProvider) {
  $locationProvider.html5Mode(true).hashPrefix('!');
  
  // For any unmatched url, redirect to /state1
  $urlRouterProvider.otherwise("/");
  //
  // Now set up the states
  $stateProvider
    .state('home', {
      url: "/",
      templateUrl: "partials/home.html"
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
      url: '/images/:id/:size',
    })
    .state('buy', {
      url: '/shop/:candidate_id',
    })
    .state('hot', {
      url: "/hot",
      templateUrl: "partials/hot.html"
    })
    .state('new', {
      url: "/new",
      templateUrl: "partials/new.html"
    })
    .state('top', {
      url: "/top",
      templateUrl: "partials/top.html"
    })
    .state('ended', {
      url: "/ended",
      templateUrl: "partials/ended.html"
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