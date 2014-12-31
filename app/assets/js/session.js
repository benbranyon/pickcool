console.log('session.js loaded');
app.config(function (ezfbProvider) {
  ezfbProvider.setInitParams({
    appId: '1497159643900204',
    version   : 'v2.2',
    status: true,
  });  
})
.run(function(ezfb,$rootScope,$http,api,$templateCache, $location) {
  $rootScope.current_user = null;
  $rootScope.accessToken = null;

  $rootScope.session_started = false;
  function updateStatus(res) 
  {
    console.log("auth.statusChange",res);
    $rootScope.accssToken = null;
    if(!res.authResponse) 
    {
      $rootScope.current_user = null;
      console.log('Unauthenticated');
      $rootScope.session_started = true;
      return;
    }
    $rootScope.accessToken = res.authResponse.accessToken;
    api.getUser(function(res) {
      if(res.status=='ok')
      {
        $rootScope.current_user = res.data;
        $rootScope.$broadcast('user', res.data);
        console.log('Authenticated');
        $rootScope.session_started = true;
      } else {
        console.log("API Error");
      }
    });
  }
  
  ezfb.getLoginStatus(updateStatus);
  
  ezfb.Event.subscribe('auth.statusChange', updateStatus);
  
  ezfb.Event.subscribe('auth.authResponseChanged', function (statusRes) {
    console.log('xx authResponseChanged');
    console.log(statusRes);
  });  
  
  $rootScope.location = $location;

  $rootScope.login = function () {
    var serialize = function(obj) {
      var str = [];
      for(var p in obj)
        if (obj.hasOwnProperty(p)) {
          str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        }
      return str.join("&");
    };
    qs = {
      client_id: '1497159643900204',
      redirect_uri: $location.absUrl(),
      scope: 'public_profile,email,user_likes',
      default_audience: 'everyone',
      auth_type: 'rerequest',
    };
    window.location = "https://www.facebook.com/dialog/oauth?"+serialize(qs);
    return;
    ezfb.login(null, {
     scope: 'public_profile,email,user_likes',
     default_audience: 'everyone',
    });
    return;
    
  };

  $rootScope.logout = function () {
   ezfb.logout();
   window.location = '/';
  };
});
