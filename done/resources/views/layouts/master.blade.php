<html>
  <head>
    <title>Done Reports</title>
    <link rel="stylesheet" type="text/css" href="/semantic-ui/semantic.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/styles.css">
    <script src="/assets/jquery-2.2.0.min.js"></script>
  </head>
  <body class="">

    <header class="ui fixed inverted main menu">
      <div class="ui container">
        <a href="javascript:open_nav();" class="icon item collapse-icon"><i class="content icon"></i></a>
        <a href="/" class="item">
          <img class="logo" src="/assets/compass.svg">
          <span style="padding-left: 0.7em;">Done!</span>
        </a>
        <a href="/dashboard" class="item">Dashboard</a>
        <div class="ui simple dropdown item right">
          Your Account <i class="dropdown icon"></i>
          <div class="menu">
            <a class="item" href="/auth/logout">Sign Out</a>
          </div>
        </div>
      </div>
    </header>

    <div id="page">
      <div id="page_contents">
        <nav>
          @section('sidebar')
          @show
        </nav>
        <div id="content">
          @yield('content')
        </div>
      </div>
    </div>

    <script src="/semantic-ui/semantic.min.js"></script>
    <script>
    function open_nav() {
      $("body").toggleClass("nav_open");
    }
    </script>
  </body>
</html>