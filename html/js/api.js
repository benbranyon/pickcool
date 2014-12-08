app.service('api', function(ezfb, $http, $rootScope) {
  this.getUser = function(success, error)
  {
    console.log("getUser");
    $http.get(API_ENDPOINT+'/user', 
      {
        'params': {
          'accessToken': $rootScope.accessToken,
        }
      }
    )
    .success(function(data, status, headers, config) {
      console.log("getUser responded with ", data);
      success(data);
    })
    .error(function(data, status, headers, config) {
      console.log('getUser failed', data, status);
      if (error) error(data);
    });
  };
  
  this.createContest = function(candidates, success, error) {
    $http.get(API_ENDPOINT+'/my/contests/create', 
      {
        'params': {
          'accessToken': $rootScope.accessToken,
          'candidates': JSON.stringify(candidates)
        }
      }
    )
    .success(function(data, status, headers, config) {
      console.log("createContest responded with ", data);
      if (success) success(data);
    })
    .error(function(data, status, headers, config) {
      console.log('createContest failed', data, status);
      if (error) error(data);
    });
  };
  
  this.getMyContests = function(success, error) {
    $http.get(API_ENDPOINT+'/my/contests', 
      {
        'params': {
          'accessToken': $rootScope.accessToken,
          'candidates': JSON.stringify($rootScope.candidates)
        }
      }
    )
    .success(function(data, status, headers, config) {
      console.log("getMyContests responded with ", data);
      if (success) success(data);
    })
    .error(function(data, status, headers, config) {
      console.log('getMyContests failed', data, status);
      if (error) error(data);
    });
  };
  
  this.vote = function(candidate, success, error) {
    $http.get(API_ENDPOINT+'/vote', 
      {
        'params': {
          'accessToken': $rootScope.accessToken,
          'c': candidate.id,
        }
      }
    )
    .success(function(data, status, headers, config) {
      console.log("vote responded with ", data);
      if (success) success(data);
    })
    .error(function(data, status, headers, config) {
      console.log('vote failed', data, status);
      if (error) error(data);
    });
  };
});