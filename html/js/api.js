app.service('api', function(ezfb, $http, $rootScope) {
  var api_lowevel = function(args) {
    ezfb.getLoginStatus().then(function(res) {
      $rootScope.accssToken = null;
      if(!res.authResponse) 
      {
        $rootScope.current_user = null;
        return;
      }
      $rootScope.accessToken = res.authResponse.accessToken;
      console.log("Access token is ", $rootScope.accessToken);
      var params = {
        'accessToken': $rootScope.accessToken,
      };
      angular.extend(params, args.params);
      $http.get(API_ENDPOINT+args.path, 
        {
          'params': params
        }
      )
      .success(function(data, status, headers, config) {
        console.log(args.name + " responded with ", data);
        if (args.success) args.success(data);
      })
      .error(function(data, status, headers, config) {
        console.log('getUser failed', data, status);
        if (args.error) args.error(data);
      });
    });
  }
  
  this.getUser = function(success, error)
  {
    api_lowevel({'name': 'getUser', 'path': '/user', 'success': success, 'error': error});
  };
  
  this.createContest = function(contest, success, error) {
    api_lowevel({'name': 'createContest', 'path': '/my/contests/create', 'params': {'contest': JSON.stringify(contest) }, 'success': success, 'error': error});
  };
  
  this.getMyContests = function(success, error) {
    api_lowevel({'name': 'getMyContests', 'path': '/my/contests', 'success': success, 'error': error});
  };

  this.getFeaturedContests = function(success, error) {
    api_lowevel({'name': 'getFeaturedContests', 'path': '/contests/featured', 'success': success, 'error': error});
  };

  this.getContest = function(contest_id, success, error) {
    api_lowevel({'name': 'getContest', 'path': '/contests/'+contest_id, 'success': success, 'error': error});
  };
  this.getContests = function(type, success, error) {
    api_lowevel({'name': 'getContests', 'path': '/contests/'+type, 'success': success, 'error': error});
  };
  
  this.vote = function(candidate_id, success, error) {
    api_lowevel({'name': 'vote', 'path': '/vote',  'params': {'c': candidate_id }, 'success': success, 'error': error});
  };
  
  
});