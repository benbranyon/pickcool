app.service('api', function(ezfb, $http, $rootScope) {
  var api_lowevel = function(args) {
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
  }
  
  this.getUser = function(success, error)
  {
    api_lowevel({'name': 'getUser', 'path': '/user', 'success': success, 'error': error});
  };
  
  this.createContest = function(candidates, success, error) {
    api_lowevel({'name': 'createContest', 'path': '/my/contests/create', 'params': {'candidates': JSON.stringify(candidates) }, 'success': success, 'error': error});
  };
  
  this.getMyContests = function(success, error) {
    api_lowevel({'name': 'getMyContests', 'path': '/my/contests', 'success': success, 'error': error});
  };
  
  this.vote = function(candidate, success, error) {
    api_lowevel({'name': 'vote', 'path': '/vote',  'params': {'c': candidate.id }, 'success': success, 'error': error});
  };
});