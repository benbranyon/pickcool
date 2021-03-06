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
            if (console[method]) {
                console[method] = noop;
            }
          }
      }());      
    </script>
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
    <meta name="description" content="Where you pick what’s cool." />
    @yield('head')

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.2.0/css/font-awesome.min.css">
    <style type="text/css">
      .logo-img {width:150px;float:left;}
      .navbar-brand span {float:left;margin-left:5px;margin-top:5px;}
      .navbar-right {margin-right:10px;margin-top:14px;}
      .archived { background-color: rgb(255, 226, 226);}
      .table-hover>tbody>tr.archived:hover { background-color: rgb(253, 170, 170);}
      .left-margin-10 { margin-left: 10px; margin-top: 10px; }
    </style>
    <script src="//cdn.jsdelivr.net/jquery/2.1.3/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
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
         js.src = "//connect.facebook.net/en_US/sdk.js";
         fjs.parentNode.insertBefore(js, fjs);
       }(document, 'script', 'facebook-jssdk'));
    </script>

    <nav class="navbar navbar-inverse navbar-static-top" role="navigation" style="margin-bottom: 0">
      <div class="container-fluid">      
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/admin"><img class="logo-img" alt="Pick.Cool" src="/assets/img/pick-cool-logo.png" /><span>Admin</span></a>
            </div>
            <!-- /.navbar-header -->
            <div id="navbar" class="collapse navbar-collapse">
              <ul class="nav navbar-nav">
                <li><a href="/admin/users">Users</a></li>
                <li><a href="/admin/contests">Contests</a></li>
                <li><a href="/admin/candidates">Candidates</a></li>
                <li><a href="/admin/sponsors">Sponsors</a></li>
                <li><a href="{{{r('admin.badges')}}}">Badges</a></li>
                <li><a href="{{{r('admin.images')}}}">Images</a></li>
              </ul>
            </div>
      </div>
    </nav>

    <div class="container-fluid">
            <ul class="pull-right">
              @if(Auth::user())
               <li>Welcome, {{{Auth::user()->first_name}}}.</li>
              @endif
            </ul>
      @foreach(['success', 'warning', 'danger'] as $kind)
        @if(Session::get($kind))
          <div class="alert alert-{{{$kind}}}">
            {{{Session::get($kind)}}}
          </div>
        @endif
        <?php Session::forget($kind); ?>
      @endforeach
      @yield('content')
    </div> <!-- // Container -->

    <script src="/assets/js/echo.js"></script>

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