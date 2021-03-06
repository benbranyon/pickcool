<?php
$cache_bust = '?_r='.env('CACHE_BUST', uniqid());
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <script>
      (function() {
          var method;
          var noop = function () {};
          var methods = [
            'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
            'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
            'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
            'timeStamp', 'trace', 'warn'
          ];
          var length = methods.length;
          var console = (window.console = window.console || {});
      
          while (length--) {
            method = methods[length];
            // Only stub undefined methods.
            if (!console[method]) {
                console[method] = noop;
            }
          }
      }());      
    </script>
    @if($_ENV['BUGSNAG_ENABLED'])
      <script
      src="//d2wy8f7a9ursnm.cloudfront.net/bugsnag-2.min.js{{$cache_bust}}"
      data-apikey="{{$_ENV['BUGSNAG_API_KEY']}}">
      </script>
    @endif
    <script src="/pick.cool.js{{$cache_bust}}"></script>
    <meta property="fb:app_id" content="1497159643900204"/>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale = 1.0">
    @if(isset($contest->title))
      <meta name="description" content="{{{$contest->title}}}" /> 
    @else
      <meta name="description" content="Where you pick what's cool." />
    @endif
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.2/css/bootstrap.min.css{{$cache_bust}}">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.2.0/css/font-awesome.min.css{{$cache_bust}}">
    <link rel="stylesheet" href="/assets/css/style.css{{$cache_bust}}">
    @yield('head')

  </head>

  <body>
    <div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '1497159643900204',
          xfbml      : true,
          version    : 'v2.1',
        });
        if (window.jQuery) {  
          $(document).trigger('fbload'); 
        }
      };

      (function(d, s, id){
         var js, fjs = d.getElementsByTagName(s)[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement(s); js.id = id;
         js.src = "//connect.facebook.net/en_US/sdk.js{{$cache_bust}}";
         fjs.parentNode.insertBefore(js, fjs);
       }(document, 'script', 'facebook-jssdk'));
    </script>
    
    <nav class="navbar navbar-default navbar-fixed-top" style="{{{ $_ENV['IS_BETA'] ? "background-color: #F88" : "" }}}" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="{{r('home')}}"><img class="logo-img" alt="Pick.Cool" src="/assets/img/pick-cool-logo.png" /></a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div>
          <ul class="nav navbar-nav navbar-right">
            <li class="{{Route::currentRouteName()=='contests.live' ? 'active' : ''}}"><a href="{{{r('contests.live')}}}">Live</a></li>
            <li class="{{Route::currentRouteName()=='contests.archived' ? 'active' : ''}}"><a href="{{{r('contests.archived')}}}"  >Archived</a></li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container-fluid">
      <div id="userheader"></div>
      @yield('content')
      <script src="{{route('usercontext', ['r'=>Route::currentRouteName(), 'p'=>Route::current()->parameters()])}}"></script>
      
      <footer class="footer">
        <div class="row">
          <div class="col-sm-12">
            <ul class="pull-left nav nav-pills footer-nav">
              <li>&copy; Pick.Cool {{date('Y')}}</li>
            </ul>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 like-button-wrapper">
            <div class="fb-like" data-href="https://www.facebook.com/pages/PickCool/310629329135330" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 text-center">
            <ul class="nav nav-pills footer-nav">
              <li><a href="/privacy">Privacy Policy</a></li>
              <li><a href="/terms">Terms of Service</a></li>
              <li><a href="/advertise">Advertise</a></li>
              <li><a href="/faq"><i class="fa fa-question-circle"></i> F.A.Q.</a></li>
            </ul>
          </div>
        </div>
      </footer>
    </div> <!-- // Container -->
    @yield('foot')
    
    <script src="/assets/js/echo.js{{$cache_bust}}"></script>

    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-57868973-1', 'auto');
      ga('send', 'pageview');
    </script>

  </body>
</html>
