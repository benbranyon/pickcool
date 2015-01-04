<!DOCTYPE html>
<html lang="en">
  <head>
    @if($_ENV['BUGSNAG_ENABLED'])
      <script
      src="//d2wy8f7a9ursnm.cloudfront.net/bugsnag-2.min.js"
      data-apikey="{{$_ENV['BUGSNAG_API_KEY']}}">
      </script>
    @endif
    <meta property="fb:app_id" content="1497159643900204"/>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale = 1.0">
    @yield('head')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.2.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="/assets/css/style.css">

  </head>

  <body>
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=1497159643900204&version=v2.1";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
    
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="{{route('home')}}">Pick.Cool</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div>
          <ul class="nav navbar-nav navbar-right">
            <li class="{{(Route::currentRouteName()=='home' || Route::currentRouteName()=='hot') ? 'active' : ''}}"><a href="{{{route('contests.hot')}}}">Hot</a></li>
            <li class="{{Route::currentRouteName()=='new' ? 'active' : ''}}"><a href="{{{route('contests.new')}}}"  >New</a></li>
            <li class="{{Route::currentRouteName()=='hot' ? 'active' : ''}}"><a href="{{{route('contests.top')}}}" >Top</a></li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container-fluid">
      @foreach(['success', 'warning', 'danger'] as $kind)
        @if(Session::get($kind))
          <div class="alert alert-{{{$kind}}}">
            {{{Session::get($kind)}}}
          </div>
        @endif
        <?php Session::forget($kind); ?>
      @endforeach
          @yield('content')
          
          <div class="clearfix">
            <ul class="login-list list-inline pull-right">
              @if(Auth::check() && Auth::user()->is_contributor)
                <li ng-if="current_user.is_contributor" class="btn btn-xs btn-primary" ui-sref="contests-create"><i class="fa fa-plus"></i> Submit</li>
              @endif
            </ul>
          </div>

          <footer class="footer">
            <div class="row">
              <div class="col-sm-12">
                <ul class="pull-left nav nav-pills footer-nav">
                  <li>&copy; pick.cool 2014</li>
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
                  <li><a ng-href="/privacy">Privacy Policy</a></li>
                  <li><a ng-href="/terms">Terms of Service</a></li>
                </ul>
              </div>
            </div>

            <script>
              (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
              (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
              m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
              })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

              ga('create', 'UA-57868973-1', 'auto');
              ga('send', 'pageview');
            </script>

          </footer>

        </div>
      </div>
      
    </div> <!-- // Container -->
    
    <div id="login_dialog" class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title">Connect via Facebook</h4>
          </div>
          <div class="modal-body">
            <p>To vote in a contest or enter a contest, you must connect via Facebook.</p>
            <p style="text-align: center">
              <button class="btn btn-primary btn-xl" ng-click="login()" ng-if="!is_user_logged_in"><i class="fa fa-facebook-square"></i> Connect via Facebook</button>
            </p>
            <p class="small">Your privacy and the security of this voting platform is our priority. Please see our <a href="/privacy">privacy policy</a> for more information.
            </p>
            
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  </body>
</html>
