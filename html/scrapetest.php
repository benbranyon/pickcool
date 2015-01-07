<?php
  
  require('../vendor/autoload.php');
          $client = new \GuzzleHttp\Client();
        $client->post('https://graph.facebook.com', ['query'=>[
          'id'=>'http://next.pick.cool/est/29/northern_nevada_s_favorite_female_models',
          'scrape'=>'true',
        ]]);
