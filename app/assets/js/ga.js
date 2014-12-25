console.log('ga.js loaded');
app.run(function($rootScope, $location, $window) {
  $rootScope.$on('$stateChangeSuccess', function(event) {
    if (!$window.ga) return;
    $window.ga('send', 'pageview', { page: $location.path() });
  });
});
