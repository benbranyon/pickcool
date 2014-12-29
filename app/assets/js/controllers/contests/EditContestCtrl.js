console.log('ContestViewCtrl.js loaded');
app.controller('EditContestCtrl', function ($scope, $state, $stateParams, api) {
  $scope.add = function() {
    $scope.contest.candidates.push({});
  };
  
  $scope.operation = 'Edit';
  
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
      description: edit(res.data.description),
      password: edit(res.data.password),
      candidates: [],
    };
    angular.forEach(res.data.candidates, function(candidate, idx) {
      contest.candidates.push({
        id: edit(candidate.id),
        buy_url: edit(candidate.original_buy_url),
        image_url: edit(candidate.original_image_url),
        buy_text: edit(candidate.buy_text),
        name: edit(candidate.name),
      });
    });
    console.log(contest);
    $scope.contest = contest;
  });

  $scope.save = function($event) {
    $scope.saving = true;
    api.saveContest($scope.contest,
      function(res) {
        $scope.saving = false;
        $scope.contest = res.data;
        if(!res.error_message)
        {
          $state.go('contests-view', {contest_id: $scope.contest.id, slug: $scope.contest.slug})
          return;
        }
      }
    );
  }
})