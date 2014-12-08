app.controller('CreateContestCtrl', function ($scope, $state, api) {
  $scope.candidates = [
    {'amazon_url': 'http://www.amazon.com/s/?_encoding=UTF8&camp=1789&creative=390957&field-keywords=taylor%20swift&linkCode=ur2&tag=benallfree-20&url=search-alias%3Daps&linkId=TRUKIE3Z25IK2IHK', "image_url":"http:\/\/v2.7-beta.clipbucket.com\/files\/photos\/2014\/05\/16\/1400228168b6b742_l.jpg","vote_count":747,"id":1,"name":"Taylor Swift"},
    {'amazon_url': 'http://www.amazon.com/s/?_encoding=UTF8&camp=1789&creative=390957&field-keywords=justin%20bieber&linkCode=ur2&rh=i%3Aaps%2Ck%3Ajustin%20bieber&sprefix=justin%20bi%2Caps%2C429&tag=benallfree-20&url=search-alias%3Daps&linkId=WDZIWQQER6STSOST', "image_url":"http:\/\/assets-s3.usmagazine.com\/uploads\/assets\/articles\/74065-justin-bieber-apologizes-after-offensive-n-word-video-surfaces\/1401970999_justin-bieber-lg.jpg","vote_count":223,"id":2,"name":"Justin Beiber"},
    {'amazon_url': 'http://www.amazon.com/s/?_encoding=UTF8&ajr=0&camp=1789&creative=390957&field-keywords=britney%20spears&linkCode=ur2&rh=i%3Aaps%2Ck%3Abritney%20spears&sprefix=britney%20spears%2Cdigital-music%2C199&tag=benallfree-20&url=search-alias%3Daps&linkId=OIPNX5Y3XFXFRV3O', "image_url":"https:\/\/pbs.twimg.com\/profile_images\/426108979186384896\/J3JDXvs4_400x400.jpeg","vote_count":463,"id":3,"name":"Britney Spears"},
    {'amazon_url': 'http://www.amazon.com/s/?_encoding=UTF8&camp=1789&creative=390957&field-keywords=justin%20timberlake&linkCode=ur2&sprefix=justin%20tim%2Cdigital-music%2C216&tag=benallfree-20&url=search-alias%3Ddigital-music&linkId=AEGQJMRSWKLBHEAD', "image_url":"http:\/\/www.billboard.com\/files\/media\/justin-timberlake-2013-suite-tie-650-430.jpg","vote_count":993,"id":4,"name":"Justin Timberlake"}
  ];
  
  $scope.add = function() {
    $scope.candidates.push({'id': $scope.candidates.length+1});
  };
  
  $scope.save = function($event) {
    api.createContest($scope.candidates, function(res) {
      if(res.error_message)
      {
        for(var i in res.error_message)
        {
          var error_bag = res.error_message[i];
          $scope.candidates[error_bag.id-1].error_message = error_bag.messages;
        }
        $($event.currentTarget).ladda().ladda('stop');
      } else {
        $state.go('my-contests');
      }
    });
  }
})