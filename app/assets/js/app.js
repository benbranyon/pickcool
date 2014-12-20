var current_user = {};

if(BUGSNAG_ENABLED)
{
  angular.module('exceptionOverride', []).factory('$exceptionHandler', function () {
    return function (exception, cause) {
      Bugsnag.notifyException(exception, {diagnostics:{cause: cause}})
    };
  });
}


var app = angular.module('pickCoolApp', ['ezfb', 'ui.router', 'ng', 'angular-inview']);