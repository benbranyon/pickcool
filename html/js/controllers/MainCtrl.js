app.controller('MainCtrl', function ($state, $scope, $window, $location, api) {
  console.log('MainCtrl');
  
  $scope.state = $state;

  $scope.share = function () {
    ezfb.ui(
     {
      method: 'feed',
      name: 'angular-easyfb API demo',
      picture: 'http://v2.7-beta.clipbucket.com/files/photos/2014/05/16/1400228168b6b742_l.jpg',
      link: 'http://pick.cool/vote?/qclqht?p=preview',
      description: 'Taylor Siwft is WAAAY better than Justin Beiber! Help me prove it by voting and sharing.'
     },
     function (res) {
      console.log(res);
      // res: FB.ui response
     }
    );
  };
  
  $scope.vote = function(c) {
    if($scope.current_user)
    {
      $('.candidate').removeClass('selected');
      $('#c_'+c.id).addClass('selected');
      $('#vote_step_1').modal();
      $scope.current_selection = c;
      api.vote(c);
    } else {
     $('#login_dialog').modal();
    }
    console.log(c);
  };

  $scope.$watch('current_user', function(newVal,oldVal,scope) {
    if($scope.current_user) {
      $('#login_dialog').modal('hide');
    }
  });
  $scope.candidates = [];
});

