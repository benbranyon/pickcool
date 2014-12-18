<!DOCTYPE html>
<html lang="en" ng-app="pickCoolApp">
  <head>
    <script
  src="//d2wy8f7a9ursnm.cloudfront.net/bugsnag-2.min.js"
  data-apikey="95959bc4ce4684734053839959bc0273">
</script>
    <script>
      var CP_DEBUG=<?php echo($_ENV['JS_DEBUG'] ? 'true' : 'false') ?>;
    </script>
    <base href="/">
    <meta property="fb:app_id" content="1497159643900204"/>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>pick.cool</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="/lib/bootstrap-3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/lib/font-awesome-4.2.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/lib/ladda-bootstrap-14.12.5/dist/ladda-themeless.min.css">

    <!-- 
    Core libs
    -->
    <script src="/lib/jquery-2.1.1.min.js"></script>
    <script src="/lib/angular-1.3.5.min.js"></script>
    <script src="/lib/angular-sanitize.min.js"></script>
    <script src="/lib/angular-flash.min.js"></script>
    <script src="/lib/angular-ui-router-0.2.12.min.js"></script>
    <script src="/lib/angular-easyfb-1.2.1/angular-easyfb.js"></script>
    <script src="/lib/ladda-bootstrap-14.12.5/dist/spin.min.js"></script>
    <script src="/lib/ladda-bootstrap-14.12.5/dist/ladda.min.js"></script>
    <script src="/lib/ladda-bootstrap-14.12.5/dist/ladda.jquery.min.js"></script>

    <!-- 
    Custom code
    -->
    <script src="/js/env.js"></script>
    <script src="/js/app.js"></script>
    <script src="/js/api.js"></script>
    <script src="/js/routes.js"></script>
    <script src="/js/controllers/MainCtrl.js"></script>
    <script src="/js/controllers/contests/CreateContestCtrl.js"></script>
    <script src="/js/controllers/contests/EditContestCtrl.js"></script>
    <script src="/js/controllers/contests/ContestViewCtrl.js"></script>
    
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="/lib/aFarkas-html5shiv-3.7.2/dist/html5shiv.min.js"></script>
      <script src="/lib/Respond-1.4.2/dest/respond.min.js"></script>
    <![endif]-->
      <script>
      $(document).on('click','.navbar-collapse.in',function(e) {
          if( $(e.target).is('a, button') && ( $(e.target).attr('class') != 'dropdown-toggle' ) ) {
              $(this).collapse('hide');
          }

      });
      </script>
      <?php if($_ENV['BETA']): ?>
        <style>
          body
          {
            background-color: rgb(255, 186, 155);
          }
        </style>
      <?php endif; ?>
  </head>

  <body ng-controller="MainCtrl">
    <div class="container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <span class="text-primary">pick.cool</span>
          <span class="small text-muted">Vote and watch social contests in real time.</span>
          
        </div>
      </div>
      <ul class="list-inline nav">
        <li class="btn  {{state.is('home') || state.is('hot') ? 'btn-primary' : 'btn-default'}}" ui-sref="hot" ng-click="state.reload()">Hot</li>
        <li class="btn  {{state.is('new') ? 'btn-primary' : 'btn-default'}}" ui-sref="new"  ng-click="state.reload()">New</li>
        <li class="btn  {{state.is('top') ? 'btn-primary' : 'btn-default'}} text-primary" ui-sref="top" ng-click="state.reload()">Top</li>
      </ul>
      <ul class="list-inline pull-right">
        <li ng-if="!current_user" class="pull-right btn btn-xs btn-primary" ng-click="login()" ><i class="fa fa-facebook-square"></i> Login</li>
        <li ng-if="current_user.is_contributor" class="btn btn-xs btn-primary" ui-sref="contests-create"><i class="fa fa-plus"></i> Submit</li>
        <li ng-if="current_user" class="btn btn-xs btn-danger" ng-click="logout()" ><i class="fa fa-sign-out"></i> Logout</li>
      </ul>

      <div class="row">
        <div class="col-xs-12 ">
          <div flash-messages></div>
          <div ui-view ng-if="session_started"></div>

          <div class="footer">
            <p>&copy; pick.cool 2014</p>
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
    <script src="/lib/bootstrap-3.2.0/js/bootstrap.min.js"></script>
  </body>
</html>
