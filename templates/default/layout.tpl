<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>{app.meta_title}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="{app.meta_description}">
        <meta name="author" content="{app.meta_author}">

        <!-- Le styles -->
        <link href="{app.theme}lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="{app.theme}css/layout.css" rel="stylesheet">

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!-- Fav and touch icons
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
        <link rel="shortcut icon" href="../assets/ico/favicon.png"> -->
    </head>

    <body>

        <div class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <a href="{base_uri}" class="navbar-brand">speedcode-box</a>
                {block.menus.main}
            </div>
        </div>
        
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    {block.login.userbar}
                </div>
                <div class="col-md-9">
                    {app.breadcrumb}
                    <div class="page-header">
                        <h1>{app.title}</h1>
                    </div>
                    {app.error}
                    {app.content}
                </div>
            </div>
        </div> <!-- /container -->
        
        <div id="footer" class="navbar navbar-default navbar-fixed-bottom">
            <div id="copyright">{app.name}-{app.version} {base_copyright}</div>
        </div>

        <!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="{app.theme}lib/jquery/jquery-1.10.2.min.js"></script>
        <script src="{app.theme}lib/bootstrap/js/bootstrap.min.js"></script>

    </body>
</html>
