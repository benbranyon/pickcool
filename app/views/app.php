<!DOCTYPE html>
<html lang="en" ng-app="pickCoolApp">
  <head>
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
    <script src="/lib/http-auth-interceptor.js"></script>
    <script src="/lib/angular-ui-router-0.2.12.min.js"></script>
    <script src="/lib/angular-easyfb-1.2.1/angular-easyfb.js"></script>
    <script src="/lib/RainbowVis-JS-14.10.26/rainbowvis.js"></script>
    <script src="/lib/ladda-bootstrap-14.12.5/dist/spin.min.js"></script>
    <script src="/lib/ladda-bootstrap-14.12.5/dist/ladda.min.js"></script>
    <script src="/lib/ladda-bootstrap-14.12.5/dist/ladda.jquery.min.js"></script>

    <!-- 
    Custom code
    -->
    <script src="/js/env.js"></script>
    <script src="/js/rainbow.js"></script>
    <script src="/js/app.js"></script>
    <script src="/js/api.js"></script>
    <script src="/js/routes.js"></script>
    <script src="/js/controllers/MainCtrl.js"></script>
    <script src="/js/controllers/my/contests/CreateContestCtrl.js"></script>
    <script src="/js/controllers/my/contests/ListContestsCtrl.js"></script>
    <script src="/js/controllers/HotCtrl.js"></script>
    <script src="/js/controllers/HomeCtrl.js"></script>
    <script src="/js/controllers/contests/ContestViewCtrl.js"></script>
    
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="/lib/aFarkas-html5shiv-3.7.2/dist/html5shiv.min.js"></script>
      <script src="/lib/Respond-1.4.2/dest/respond.min.js"></script>
    <![endif]-->
  </head>

  <body ng-controller="MainCtrl">
    <div class="container">
      <div class="row" style="height: 50px">
        <div class="col-lg-6">
          <ul class="nav nav-pills">
            <li role="presentation" ng-class="{'active': state.is('home')}"><a ui-sref="home">Home</a></li>
            <li role="presentation" ng-if="current_user.is_beta" ng-class="{'active': state.is('hot')}"><a ui-sref="hot">Hot</a></li>
            <li role="presentation" ng-if="current_user.is_beta" ng-class="{'active': state.is('new')}"><a ui-sref="new">New</a></li>
            <li role="presentation" ng-if="current_user.is_beta" ng-class="{'active': state.is('top')}"><a ui-sref="top">Top</a></li>
            <li role="presentation" ng-if="current_user.is_beta" ng-class="{'active': state.is('ended')}"><a ui-sref="ended">Ended</a></li>
          </ul>
        </div>
        <div class="col-lg-6"  style="text-align: right">
          <button class="btn btn-primary btn-xs" ng-click="login()" ng-if="!current_user"><i class="fa fa-facebook-square"></i> Login</button>
          <span ng-if="current_user">
            <a class="btn btn-primary btn-xs " ui-sref="my-contests-create" ng-if="current_user.is_contributor"><i class="fa fa-plus"></i> Create Contest</a>
            <a class="btn btn-primary btn-xs" ui-sref="my-contests" ng-if="current_user.is_contributor"><i class="fa fa-th-list"></i> My Contests</a>
            <a class="btn btn-danger btn-xs" ng-click="logout()" ><i class="fa fa-sign-out"></i> Logout</a>
            <br/>
            Hello, {{current_user.first_name}}. We're watching you.
          </span>
        </div>
      </div>

      <div ui-view></div>

      <div class="footer">
        <p>&copy; pick.cool 2014</p>
      </div>

    </div> <!-- /container -->
    
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
