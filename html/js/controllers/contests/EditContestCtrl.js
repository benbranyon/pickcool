app.controller('EditContestCtrl', function ($scope, $state, $stateParams, api, $flash) {
  $scope.add = function() {
    $scope.contest.candidates.push({'id': $scope.candidates.length+1});
  };
  
  api.getContest($stateParams.contest_id, function(res) {
    function edit(val) {
      return {
        value: val,
        errors: [],
      };
    }
    var contest = {
      id: edit(res.data.id),
      title: edit(res.data.title),
      candidates: [],
    };
    angular.forEach(res.data.candidates, function(candidate, idx) {
      contest.candidates.push({
        id: edit(candidate.id),
        buy_url: edit(candidate.original_buy_url),
        image_url: edit(candidate.original_image_url),
        name: edit(candidate.name),
      });
    });
    console.log(contest);
    $scope.contest = contest;
  });

  $scope.save = function($event) {
    api.saveContest($scope.contest,
      function(res) {
        $($event.currentTarget).ladda().ladda('stop');
        $scope.contest = res.data;
        if(!res.error_message)
        {
          $flash('Saved.', {type: 'success'});
          return;
        }
      }
    );
  }
})