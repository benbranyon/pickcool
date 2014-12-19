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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

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
          <a class="navbar-brand" href="#">Pick.Cool</a>
          <p class="brand-slogan">
            <span class="small text-muted">Vote and watch social contests in real time.</span>
          </p>
        </div>
        <div id="navbar" class="navbar pull-right">
          <ul class="login-list list-inline ">
            <li ng-if="!current_user" class="pull-right btn btn-xs btn-primary" ng-click="login()" ><i class="fa fa-facebook-square"></i> Login</li>
            <li ng-if="current_user.is_contributor" class="btn btn-xs btn-primary" ui-sref="contests-create"><i class="fa fa-plus"></i> Submit</li>
            <li ng-if="current_user" class="btn btn-xs btn-danger" ng-click="logout()" ><i class="fa fa-sign-out"></i> Logout</li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container-fluid">      
      <ul class="list-inline nav">
        <li class="btn  @{{state.is('home') || state.is('hot') ? 'btn-primary' : 'btn-default'}}" ui-sref="hot" ng-click="state.reload()">Hot</li>
        <li class="btn  @{{state.is('new') ? 'btn-primary' : 'btn-default'}}" ui-sref="new"  ng-click="state.reload()">New</li>
        <li class="btn  @{{state.is('top') ? 'btn-primary' : 'btn-default'}} text-primary" ui-sref="top" ng-click="state.reload()">Top</li>
      </ul>

      <div class="row">
        <div class="col-xs-12 ">
          <div flash-messages></div>
          <div ui-view ng-if="session_started"></div>

          <div class="footer">
            <ul class="pull-left nav nav-pills footer-nav">
              <li>&copy; pick.cool 2014</li>
              <li>v{{$_ENV['ETAG']}}</li>
            </ul>
            <ul class="nav nav-pills footer-nav">
              <li><a ng-href="/privacy">Privacy Policy</a></li>
              <li><a ng-href="/terms">Terms of Service</a></li>
            </ul>
          </div>
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
            <p>Hold on, Stan! You have to be logged in before you can spit with us.</p>
            <button class="btn btn-primary btn-xs" ng-click="login()" ng-if="!is_user_logged_in"><i class="fa fa-facebook-square"></i> Login</button>
            
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  </body>
</html>
