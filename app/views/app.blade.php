<!DOCTYPE html>
<html lang="en" ng-app="pickCoolApp">
  <head>
    @if($_ENV['BUGSNAG_ENABLED'])
      <script
      src="//d2wy8f7a9ursnm.cloudfront.net/bugsnag-2.min.js"
      data-apikey="95959bc4ce4684734053839959bc0273">
      </script>
    @endif
    <base href="/">
    <meta property="fb:app_id" content="1497159643900204"/>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale = 1.0">

    <title>pick.cool</title>

    <link rel="stylesheet" href="/{{$_ENV['ETAG']}}/assets/css/app.css">
    <script src="/{{$_ENV['ETAG']}}/assets/js/app.js"></script>
    
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="/assets/js/ie_compat.js"></script>
    <![endif]-->

  </head>

  <body ng-controller="MainCtrl" id="top">
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation" ng-click="scrollTop()">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="/">Pick.Cool</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div>
          <ul class="nav navbar-nav navbar-right">
            <li class="@{{state.is('home') || state.is('hot') ? 'active' : ''}}"><a ui-sref="hot" ng-click="state.reload()">Hot</a></li>
            <li class="@{{state.is('new') ? 'active' : ''}}"><a ui-sref="new"  ng-click="state.reload()">New</a></li>
            <li class="@{{state.is('top') ? 'active' : ''}}"><a ui-sref="top" ng-click="state.reload()">Top</a></li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container-fluid">

      <div class="row">
        <ul class="login-list list-inline pull-right">
          <li ng-if="current_user.is_contributor" class="btn btn-xs btn-primary" ui-sref="contests-create"><i class="fa fa-plus"></i> Submit</li>
        </ul>
      </div>

      <div class="row">
        <div class="col-xs-12 ">
          <div flash-messages></div>
          <div ui-view ng-if="session_started"></div>

          <footer class="footer">
            <div class="row">
              <div class="col-sm-12">
                <ul class="pull-left nav nav-pills footer-nav">
                  <li>&copy; pick.cool 2014</li>
                  <li>v{{$_ENV['ETAG']}}</li>
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
