<!DOCTYPE html>
<html lang="en" ng-app="pickCoolApp">
  <head>
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
  </head>

  <body ng-controller="MainCtrl">
    <nav class="navbar navbar-default navboar-xs" role="navigation">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">pick.cool</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            <li ng-class="{active: state.is('home') || state.is('hot')}"><a ui-sref="home">Hot</a></li>
            <li ng-class="{active: state.is('new')}"><a ui-sref="new">New</a></li>
            <li ng-class="{active: state.is('top')}"><a ui-sref="top">Top</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li ng-if="!current_user">
              <button class="btn btn-primary btn-xs navbar-btn" ng-click="login()" ><i class="fa fa-facebook-square"></i> Login</button>
            </li>
            <li ng-if="current_user">
              <button class="btn btn-primary btn-xs navbar-btn" ui-sref="contests-create" ng-if="current_user.is_contributor"><i class="fa fa-plus"></i> Create Contest</button>
            </li>
            <li ng-if="current_user">
              <button class="btn btn-danger btn-xs navbar-btn" ng-click="logout()" ><i class="fa fa-sign-out"></i> Logout</button>
            </li>
          </ul>
          <p class="navbar-text navbar-right hidden-xs" ng-if="current_user">Hello, {{current_user.first_name}}. We're watching you.</p>
          
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>
    <div class="container-fluid">
      <div class="row">
        <div class="col-xs-10 col-offset-1">
          <div class="small text-muted">Vote and watch social contests in real time.</div>
        </div>
      </div>
      

      <div class="row">
        <div class="col-xs-12 col-lg-10 col-lg-offset-1">
          <div class="row">
            <div class="col-xs-12">
              <div flash-messages></div>
              <div ui-view ng-if="session_started"></div>
            </div>
          </div>

          <div class="footer">
            <p>&copy; pick.cool 2014</p>
          </div>

          </div> <!-- /container -->
        </div>
      </div>
    </div>
    
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
