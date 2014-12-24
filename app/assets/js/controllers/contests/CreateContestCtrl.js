console.log('ContestViewCtrl.js loaded');
app.controller('CreateContestCtrl', function ($scope, $state, api) {
  $scope.add = function() {
    $scope.contest.candidates.push({'id': $scope.candidates.length+1});
  };
  
  $scope.operation = 'Create';
  $scope.contest = {
    'title': {
      'value': 'my new contest',
      'errors': [],
    },
    'candidates': 
    [  
         {  
            'buy_url': {
              'value': 'http://www.amazon.com/s/?_encoding=UTF8&camp=1789&creative=390957&field-keywords=taylor%20swift&linkCode=ur2&tag=benallfree-20&url=search-alias%3Daps&linkId=TRUKIE3Z25IK2IHK',
              'errors': [],
            },
            "image_url": {
              'value': "http:\/\/v2.7-beta.clipbucket.com\/files\/photos\/2014\/05\/16\/1400228168b6b742_l.jpg",
              'errors': [],
            },
            "name": {
              'value': "Taylor Swift",
              'errors': [],
            },
            "buy_text": {
              'value': "fa-shopping-cart",
              'errors': [],
            },
         },
         {  
            'buy_url': {
              'value': 'http://www.amazon.com/s/?_encoding=UTF8&camp=1789&creative=390957&field-keywords=justin%20bieber&linkCode=ur2&rh=i%3Aaps%2Ck%3Ajustin%20bieber&sprefix=justin%20bi%2Caps%2C429&tag=benallfree-20&url=search-alias%3Daps&linkId=WDZIWQQER6STSOST',
              'errors': [],
            },
            "image_url": {
              'value': "http:\/\/assets-s3.usmagazine.com\/uploads\/assets\/articles\/74065-justin-bieber-apologizes-after-offensive-n-word-video-surfaces\/1401970999_justin-bieber-lg.jpg",
              'errors': [],
            },
            "name": {
              'value': "Justin Beiber",
              'errors': [],
            },
            "buy_text": {
              'value': "fa-shopping-cart",
              'errors': [],
            },
         },
         {  
            'buy_url': {
              'value': 'http://www.amazon.com/s/?_encoding=UTF8&ajr=0&camp=1789&creative=390957&field-keywords=britney%20spears&linkCode=ur2&rh=i%3Aaps%2Ck%3Abritney%20spears&sprefix=britney%20spears%2Cdigital-music%2C199&tag=benallfree-20&url=search-alias%3Daps&linkId=OIPNX5Y3XFXFRV3O',
              'errors': [],
            },
            "image_url": {
              'value': "https:\/\/pbs.twimg.com\/profile_images\/426108979186384896\/J3JDXvs4_400x400.jpeg",
              'errors': [],
            },
            "name": {
              'value': "Britney Spears",
              'errors': [],
            },
            "buy_text": {
              'value': "fa-shopping-cart",
              'errors': [],
            },
         },
         {  
            'buy_url': {
              'value': 'http://www.amazon.com/s/?_encoding=UTF8&camp=1789&creative=390957&field-keywords=justin%20timberlake&linkCode=ur2&sprefix=justin%20tim%2Cdigital-music%2C216&tag=benallfree-20&url=search-alias%3Ddigital-music&linkId=AEGQJMRSWKLBHEAD',
              'errors': [],
            },
            "image_url": {
              'value': "http:\/\/www.billboard.com\/files\/media\/justin-timberlake-2013-suite-tie-650-430.jpg",
              'errors': [],
            },
            "name": {
              'value': "Justin Timberlake",
              'errors': [],
            },
            "buy_text": {
              'value': "fa-shopping-cart",
              'errors': [],
            },
         }
      ]      
  };
  
  $scope.save = function($event) {
    $scope.saving=true;
    api.createContest($scope.contest,
      function(res) {
        if(!res.error_message)
        {
          var contest = res.data;
          $state.go('contests-view', {contest_id: contest.id, slug: contest.slug});
          return;
        }
        $scope.saving=false;
        angular.extend($scope.contest, res.data);
      }
    );
  }
})