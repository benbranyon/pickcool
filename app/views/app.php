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


    <script src="/lib/jquery-2.1.1.min.js"></script>
    <script src="/lib/angular-1.3.5.min.js"></script>
    <script src="/lib/angular-easyfb-1.2.1/angular-easyfb.min.js"></script>
    <script src="/lib/swfobject-1.5.min.js"></script>
    <script src="/lib/RainbowVis-JS-14.10.26/rainbowvis.js"></script>
    <script src="/js/env.js"></script>
    <script src="/js/rainbow.js"></script>
    <script src="/js/app.js"></script>
    
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
          <div class="fb-like" data-href="https://pick.cool" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
        </div>
        <div class="col-lg-6" ng-if="fb_loaded" style="text-align: right">
          <button class="btn btn-primary btn-xs" ng-click="login()" ng-if="!current_user"><i class="fa fa-facebook-square"></i> Login</button>
          <span ng-if="current_user">
            <button class="btn btn-primary btn-xs " ng-click="login()" ng-if="current_user.is_contributor"><i class="fa fa-plus"></i> Create Contest</button>
            <button class="btn btn-primary btn-xs" ng-click="login()" ng-if="current_user.is_contributor"><i class="fa fa-th-list"></i> My Contests</button>
            <button class="btn btn-danger btn-xs" ng-click="logout()" ng-if="current_user"><i class="fa fa-sign-out"></i> Logout</button>
          </span>
        </div>
      </div>

      <h2>Click to Vote. First to 1,000 votes wins</h2>
        <div ng-repeat="c in candidates" init-candidate>
          <div class="candidate" id="c_{{c.id}}" ng-click="vote(c)">
            <div class="mainContain">
              <div class="name">
                {{c.name}}
              </div>
              <div class="action_bar">
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class="glyphicon glyphicon-music" aria-hidden="true"></span> Listen <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Amazon</a></li>
                    <li><a href="#">iTunes</a></li>
                  </ul>
                </div>
                <small>{{c.vote_count}} votes</small>
              </div>
              <div class="mProgressContain">
                  <div class="mProgress0"></div>
                  <div class="mProgress1"></div>
              </div>
              <div class="roundContainX" id="profileEditX">
                <a href="#">
                  <img class='img-circle' ng-src="{{c.img}}">
                </a>
                <div class="overlay">
                  <h1>Click to Vote</h1>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
        </script>

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

    <div id="vote_step_1" class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title">You voted {{current_selection.name}}</h4>
          </div>
          <div class="modal-body">
            <h4>Now rally your friends to vote too.</h4>
            <div class="btn btn-primary" ng-click="share()">
              <i class="fa fa-facebook-square"></i> Share Now
            </div>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
                  <button class="btn btn-success" ng-click="share()">FB.ui</button>
      <div class="row">
        <div class="col-md-6">
          <h4 class="text-info">$FB.loginStatus()</h4>
          <div >{{loginStatus}}</div>
        </div>
        <div class="col-md-6">
          <h4 class="text-info">$FB.api('/me')</h4>
          <div >{{current_user}}</div>
        </div>
      </div>
      <div id="status">
      </div>      
    
    <script src="/lib/bootstrap-3.2.0/js/bootstrap.min.js"></script>
  </body>
</html>
