<?php
	session_name("singedcats");
	session_start();

	//Check login
	if(isset($_SESSION["singedcats_loggedIn"])) {
        header("Location: upload.php");
	}

	require_once 'class/DB.php';
	require_once 'class/User.php';

	//Connect to the database
	$db = new DB();
	$db->connect();

	/*//Refresh session variables if logged in
	if(isset($_SESSION['singedcats_loggedIn'])) {
		$user = unserialize($_SESSION['singedcats_user']);
		$_SESSION['singedcats_user'] = serialize($userTools->get(3));
	}*/
	
	$error = "";
	$username = "";
	$password = "";

	//Check to see if they've submitted a login form
	if(isset($_POST['signIn'])) { 
		$username = $_POST['username'];
		$password = md5($_POST['password']);
	
		if($db->login($username, $password)) { 
			//Successful login, redirect to upload page
			header("Location: upload.php");
		}
		else{
			//Error.
			header("Location: loginfail.html");
		}
	}
	
	//Check to see if they've submitted a registration form
	if(isset($_POST['register'])) {
		$data['username'] = mysql_real_escape_string($_POST['username']);
		$data['password'] = md5(mysql_real_escape_string($_POST['password']));
		$data['fname'] = mysql_real_escape_string($_POST['fname']);
		$data['lname']= mysql_real_escape_string($_POST['lname']);
		$data['email'] = mysql_real_escape_string($_POST['email']);
		
		$registrant = new User($data);
		if($registrant->register($data)){ 
			//Successful registration, redirect them to a page
			header("Location: upload.php");
		}
		else{
			//Error.
			header("Location: regfail.html");
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Singed Cat Studios : Login</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom styles for this template -->
    <link href="css/signin.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	<link href="css/override.css" rel="stylesheet">
	
	<script>
		function validate() {
			var patt = /^[A-Za-z0-9_]{3,20}$/;
			var x = document.forms["loginForm"]["username"].value;
			if (!patt.test(x)) {
				alert("Invalid characters in username.");
				return false;
			}
			patt = /^[A-Za-z0-9!@#$%^&*()_]{0,20}$/;
			x = document.forms["loginForm"]["password"].value;
			if (!patt.test(x)) {
				alert("Invalid characters in password.");
				return false;
			}
		}
	</script>
  </head>
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
                <li><a href="index.php">Home</a></li>
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
                <li class="active"><a href="login.php">Login</a></li>
                <li><a href="about.php">About</a></li>
              </ul>
            </div>
          </div>
        </div>

      </div>
    </div>
	
<div id="main" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

      <form id="loginForm" class="form-signin" role="form" action="login.php" method="post" onsubmit="return validate()">
        <h2 class="form-signin-heading">Please sign in</h2>
        <input type="text" class="form-control" style="margin-bottom:0px;" placeholder="Username" id="username" name="username" required autofocus>
        <input type="password" class="form-control" style="margin-bottom:10px;" placeholder="Password" id="password" name="password" required>
        <button class="btn btn-lg btn-warning btn-block" type="submit" name="signIn">Sign in</button>
      </form>
	
</div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>