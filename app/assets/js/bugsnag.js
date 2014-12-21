console.log('bugsnag.js loaded');
if(BUGSNAG_ENABLED)
{
  angular.module('exceptionOverride', []).factory('$exceptionHandler', function () {
    return function (exception, cause) {
      Bugsnag.notifyException(exception, {diagnostics:{cause: cause}})
    };
  });
  app.run(function($rootScope) {
    $rootScope.$on('user', function(event, user) {
      console.log('got user event');
      Bugsnag.user = {
        id: user.id,
        fb_id: user.fb_id,
        name: user.name,
        email: user.email
      };
      console.log(Bugsnag.user);
      Bugsnag.appVersion = APP_VERSION;
      Bugsnag.notify('testing');
    });
  });
}
