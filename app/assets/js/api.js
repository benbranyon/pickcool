console.log('api.js loaded');
app.service('api', function(ezfb, $http, $rootScope, $location, $state, $timeout) {
  var api_lowevel = function(args) {
    ezfb.getLoginStatus().then(function(res) {
      $rootScope.accssToken = null;
      var params = {};
      angular.extend(params, args.params);
      if(!res.authResponse) 
      {
        console.log('User is not logged in.');
        $rootScope.current_user = null;
      } else {
        $rootScope.accessToken = res.authResponse.accessToken;
        console.log("Access token is ", $rootScope.accessToken);
        params.accessToken = $rootScope.accessToken;
      }
      console.log('Posting to ', API_ENDPOINT+args.path);
      $http.post(API_ENDPOINT+args.path, params) 
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
    api_lowevel({'name': 'createContest', 'path': '/contests/create', 'params': {'contest': JSON.stringify(contest) },
      'success': success, 
      'error': error
    });
  };

  this.joinContest = function(contest_id, success, error) {
    api_lowevel({'name': 'joinContest', 'path': '/contests/join', 'params': {'contest_id': contest_id },
      'success': function(res) {
        init_contest(res.data);
        success(res);
      }, 
      'error': error
    });
  };

  this.saveContest = function(contest, success, error) {
    api_lowevel({'name': 'saveContest', 'path': '/contests/save', 'params': {'contest': JSON.stringify(contest) }, 
    'success': success, 
    'error': error});
  };
  
  this.getMyContests = function(success, error) {
    api_lowevel({'name': 'getMyContests', 'path': '/my/contests', 
      'success': function(res) {
        angular.forEach(res.data, function(contest, idx) {
          init_contest(contest);
        });
        success(res);
      }, 
    'error': error});
  };

  this.getFeaturedContests = function(success, error) {
    api_lowevel({'name': 'getFeaturedContests', 'path': '/contests/featured', 
      'success': function(res) {
        angular.forEach(res.data, function(contest, idx) {
          init_contest(contest);
        });
        success(res);
      }, 
      'error': error});
  };

  this.getContest = function(contest_id, success, error) {
    api_lowevel({'name': 'getContest', 'path': '/contests/'+contest_id, 
      'success': function(res) {
        init_contest(res.data);
        success(res);
      }, 
      'error': error});
  };
  this.getContests = function(type, success, error) {
    api_lowevel({'name': 'getContests', 'path': '/contests/'+type, 
      'success': function(res) {
        angular.forEach(res.data, function(contest, idx) {
          init_contest(contest);
        });
        success(res);
      },
      'error': error});
  };
  
  this.vote = function(candidate_id, success, error) {
    api_lowevel({'name': 'vote', 'path': '/vote',  'params': {'c': candidate_id }, 'success': success, 'error': error});
  };
  
  this.unvote = function(candidate_id, success, error) {
    api_lowevel({'name': 'unvote', 'path': '/unvote',  'params': {'c': candidate_id }, 'success': success, 'error': error});
  };
  
 
  var init_contest = function(contest)
  {
    contest.canonical_url = $location.protocol()+'://'+$location.host()+$state.href('contests-view', {'contest_id': contest.id, 'slug': contest.slug});
    // Fix up contest data
    contest.highest_vote = 0;
    contest.total_votes = 0;
    contest.current_user_writein = null;
    contest.ends_at = contest.ends_at ? moment.unix(contest.ends_at) : null;
    contest.end_check = function()
    {
      console.log('heartbeat');
      contest.can_end = false;
      contest.can_join = contest.writein_enabled && !contest.current_user_writein;
      contest.is_ended = false;
      contest.can_vote = true && (!contest.password || contest.password.length==0);
      contest.can_share = true && (!contest.password || contest.password.length==0);
      if(!contest.ends_at) return;
      contest.can_end = true;
      var now = moment();
      contest.duration = moment.duration(contest.ends_at.diff(now, 'milliseconds'));
      contest.is_ended = now > contest.ends_at;
      contest.can_vote = !contest.is_ended && (!contest.password || contest.password.length==0);
      contest.can_join = contest.writein_enabled && !contest.is_ended && !contest.current_user_writein;
      if(!contest.is_ended)
      {
        $timeout(contest.end_check,1000);
      }
    };
    angular.forEach(contest.candidates, function(c,idx) {
      if($rootScope.current_user && c.fb_id == $rootScope.current_user.fb_id)
      {
        contest.current_user_writein = c;
      }
      if (c.id == contest.current_user_candidate_id)
      {
        contest.current_user_candidate = c;
      }
      c.image = function(size) {
        if(!size) size='thumb';
        return $state.href('image-view', {'id': c.image_id, 'size': size}); 
      };
      c.buy_url = $state.href('buy', {candidate_id: c.id});
      c.share_url = function() {
       return $location.protocol()+'://'+$location.host()+$state.href('contests-share', {'contest_id': contest.id, 'slug': contest.slug, 'user_id': $rootScope.current_user.id, 'candidate_id': c.id}); 
      };
      if(c.vote_count > contest.highest_vote) contest.highest_vote = c.vote_count;
      contest.total_votes = contest.total_votes + c.vote_count;
    });
    angular.forEach(contest.sponsors, function(c,idx) {
      c.image = function(size) {
        if(!size) size='thumb';
        return $state.href('image-view', {'id': c.image_id, 'size': size}); 
      };
    });
    contest.candidates.sort(function(a,b) {
      return b.vote_count - a.vote_count;
    });
    contest.sponsors.sort(function(a,b) {
      console.log('sorting', a, b);
      return a.weight - b.weight;
    });
    contest.end_check();
  };
  
  
});