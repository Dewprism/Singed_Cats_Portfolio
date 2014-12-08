<?php
	session_name("singedcats");
	session_start();

    require_once 'class/DB.php';
	$db = new DB();
	$db->connect();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Singed Cat Studios</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
      
    <!-- Custom CSS-->
	<link href="css/override.css" rel="stylesheet">
  </head>
    
<!-- NAVBAR-->
  <body>
    <div class="navbar-wrapper">
      <div class="container">
        <div class="navbar navbar-inverse navbar-static-top" role="navigation">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.php"><img id="logo" src="images/sc_logo.jpg"></a>
            </div>
            <div class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
                <li class="active"><a href="index.php">Home</a></li>
				<li class="dropdown">
                  <a href="" class="dropdown-toggle" data-toggle="dropdown">Galleries <span class="caret"></span></a>
                  <ul class="dropdown-menu" role="menu">
                    <?php
                        $artists = $db->selectAll("users");
                        foreach ($artists as $artist) {
                            echo '<li><a href="gallery.php?artist='.$artist['id'].'">'.$artist['fname'].' '.$artist['lname'].'</a></li>';
                        }
                    ?>
                    <li class="divider"></li>
                    <li><a href="gallery.php">All Artists</a></li>
                  </ul>
                </li>
                <?php 
				if (isset($_SESSION['singedcats_userID'])) echo '<li><a href="upload.php">Upload</a></li>';
				else echo '<li><a href="login.php">Login</a></li>';
                ?>
                <li><a href="about.php">About</a></li>
                <?php
					if (isset($_SESSION["singedcats_username"]) && $_SESSION["singedcats_username"] == "dwallace") {
						echo '<li><a href="admin.php">Admin</a></li>';
					}
				?>
              </ul>
                <?php
                //Check login
                if(isset($_SESSION["singedcats_loggedIn"])) {
                    echo ' <a class="btn btn-sm btn-danger" style="float: right; margin-top: 10px;" href="logout.php">Logout</a><div style="float: right; padding-top: 15px; padding-left: 15px; padding-right: 15px; vertical-align: middle;">Welcome '.$_SESSION["singedcats_fname"].'!</div>';
                }
                ?>  
            </div>
          </div>
        </div>
      </div>
    </div>
<!-- /Navbar -->

<div id="main" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:0;">
	<div class="row" style="margin:0 auto;">
    <div class="col-lg-1 col-md-0 col-sm-0 col-xs-0"></div>
	<div class="col-lg-7 col-md-8 col-sm-8 col-xs-12">
		<!-- Carousel================================================== -->
		<div id="carousel" class="carousel slide" data-ride="carousel">
			<!-- Indicators -->
			<ol class="carousel-indicators">
				<li data-target="#carousel" data-slide-to="0" class="active"></li>
				<li data-target="#carousel" data-slide-to="1"></li>
				<li data-target="#carousel" data-slide-to="2"></li>
			</ol>
			<div class="carousel-inner">
				<div class="item active">
				<img src="images/jrose/SotC_Poster_by_FunkyJrtb.jpg" alt="First slide">
				<div class="container">
					<div class="carousel-caption">
					<h1>James <span class="text-muted">Rose</span></h1>
					<p><a class="btn btn-lg btn-warning" href="gallery.php?artist=3" role="button">Browse Gallery</a></p>
					</div>
				</div>
				</div>
				<div class="item">
				<img src="images/astoll/the_legend_of_korra_team_avatar_by_tsubasa_no_kami-d65o3uh.jpg" alt="Second slide">
				<div class="container">
					<div class="carousel-caption">
					<h1>Annie <span class="text-muted">Stoll</span></h1>
					<p><a class="btn btn-lg btn-warning" href="gallery.php?artist=4" role="button">Browse Gallery</a></p>
					</div>
				</div>
				</div>
				<div class="item">
				<img src="images/dwallace/MechanicalBiology_1024x768.jpg" alt="Third slide">
				<div class="container">
					<div class="carousel-caption">
					<h1>Danielle <span class="text-muted">Wallace</span></h1>
					<p><a class="btn btn-lg btn-warning" href="gallery.php?artist=5" role="button">Browse Gallery</a></p>
					</div>
				</div>
				</div>
			</div>
			<a class="left carousel-control" href="#carousel" role="button" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
			<a class="right carousel-control" href="#carousel" role="button" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
		</div>
		</div>
		<!-- /Carousel -->

		<!--NEWSBOX-->
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<iframe id="news" frameborder="0" src="news.html"></iframe> 
		</div>
        <!-- /NEWSBOX-->
        
        <div class="col-lg-1 col-md-0 col-sm-0 col-xs-0"></div>
	</div>

    <!-- Featurettes -->

    <div class="marketing" style="padding-left:5%; padding-right:5%;">

      <!-- Three columns of text below the carousel -->
      <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
          <img class="img-circle" src="images/jrose/pinup.jpg" alt="Generic placeholder image">
          <h2>James <span class="text-muted">Rose</span></h2>
          <p>Cartoonist and creator of webcomic "Strays."</p>
          <p><a class="btn btn-danger" href="gallery.php?artist=3" role="button">View Gallery &raquo;</a></p>
        </div><!-- /.col-lg-4 -->
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
          <img class="img-circle" src="images/astoll/ode_lady_knights_banner_by_tsubasa_no_kami-d7q0sh5.jpg" alt="Generic placeholder image">
          <h2>Annie <span class="text-muted">Stoll</span></h2>
          <p>Illustrator and designer, currently Junior Art Director at Sony Music.</p>
          <p><a class="btn btn-danger" href="gallery.php?artist=4" role="button">View Gallery &raquo;</a></p>
        </div><!-- /.col-lg-4 -->
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
          <img class="img-circle" src="images/dwallace/title.png" alt="Generic placeholder image">
          <h2>Danielle <span class="text-muted">Wallace</span></h2>
          <p>Illustrator, programmer, scientist; all-around Renaissance girl.</p>
          <p><a class="btn btn-danger" href="gallery.php?artist=5" role="button">View Gallery &raquo;</a></p>
        </div><!-- /.col-lg-4 -->
      </div><!-- /.row -->

      <!-- FOOTER -->
      <footer>
        <p class="pull-right"><a href="#">Back to top</a></p>
        <p>Site design &copy; 2014 Danielle Wallace</p>
      </footer>

    </div><!-- /Container -->
	
</div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/docs.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>