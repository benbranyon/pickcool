
app.controller('HomeCtrl', function($scope, api, $location, $state, $filter) {
  api.getFeaturedContests(function(res) {
    $scope.contests = res.data;
    angular.forEach($scope.contests, function(contest, idx) {
      contest.canonical_url = $location.protocol()+'://'+$location.host()+$state.href('contests-view', {'contest_id': contest.id, 'slug': contest.slug}),
      // Fix up contest data
      contest.highest_vote = 0;
      contest.total_votes = 0;
      angular.forEach(contest.candidates, function(c,idx) {
        c.image_url = $state.href('image-view', {'id': c.image_id, 'size': 'tiny'});
        if(c.vote_count > contest.highest_vote) contest.highest_vote = c.vote_count;
        contest.total_votes = contest.total_votes + c.vote_count;
      });
      contest.candidates = $filter('orderBy')(contest.candidates, 'vote_count', true);
    });
  });
});