console.log('bugsnag.js loaded');
if(BUGSNAG_ENABLED)
{
  angular.module('exceptionOverride', []).factory('$exceptionHandler', function () {
    return function (exception, cause) {
      Bugsnag.notifyException(exception, {diagnostics:{cause: cause}})
    };
  });
  app.run(function($rootScope) {
    $rootScope.$on('user', function() {
      console.log('got user event');
      Bugsnag.user = {
        id: res.data.id,
        name: res.data.name,
        email: res.data.email
      };
    });
  });
}
