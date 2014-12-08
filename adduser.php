<?php
    session_name("singedcats");
    session_start();

	//Check login
	if(!isset($_SESSION["singedcats_loggedIn"]) || $_SESSION["singedcats_username"] != "dwallace") {
        header("Location: login.php");
	}

	require_once 'class/DB.php';
    require_once 'class/User.php';

    $db = new DB();
	$db->connect();

    if (isset($_POST["save"])) {
        $data['username'] = mysql_real_escape_string($_POST['username']);
		$data['password'] = md5(mysql_real_escape_string($_POST['password']));
		$data['fname'] = mysql_real_escape_string($_POST['fname']);
		$data['lname']= mysql_real_escape_string($_POST['lname']);
		$data['email'] = mysql_real_escape_string($_POST['email']);
		$registrant = new User($data);
		if($registrant->register($data)){ 
            if (mkdir("images/".$data['username'])) {
                header("Location: admin.php?e=success");
            }
            else header("Location: admin.php?e=fail");
		}
		else{
			header("Location: admin.php?e=fail");
		}
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Singed Cat Studios : User Administration</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom styles for this template -->
    <link href="css/signin.css" rel="stylesheet">
	<link href="css/override.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script>
		function validate() {
			var patt = /^[A-Za-z0-9_]{3,20}$/;
			var	x = document.forms["userForm"]["username"].value;
			if (!patt.test(x)) {
				alert("Username may only contain letters, numbers, and underscores.");
				return false;
			}
			patt = /^[A-Za-z0-9!@#$%^&*()_]{6,20}$/;
			x = document.forms["userForm"]["password"].value;
            if (x == document.forms["userForm"]["passwordCheck"].value) {
                if (!patt.test(x)) {
                    alert("Password must be between 6 and 20 characters and may contain letters, numbers, and special characters.");
                    return false;
                }
            }
            else {
                alert("Password entries do not match.");
                return false;
            }
			patt = /^[A-Za-z0-9_]{2,20}$/;
			x = document.forms["userForm"]["fname"].value;
			if (!patt.test(x)) {
				alert("Invalid first name.");
				return false;
			}
			x = document.forms["userForm"]["lname"].value;
			if (!patt.test(x)) {
				alert("Invalid last name.");
				return false;
			}
			//Bootstrap automatically checks the e-mail field for validity.
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
                <li><a href="upload.php">Upload</a></li>
                <li><a href="about.php">About</a></li>
				<?php
					if ($_SESSION["singedcats_username"] == "dwallace") {
						echo '<li class="active"><a href="admin.php">Admin</a></li>';
					}
				?>
              </ul>
				<?php
                //Check login
                if(isset($_SESSION["singedcats_loggedIn"])) {
                    echo ' <a class="btn btn-sm btn-danger" style="float: right; margin-top: 10px;" href="logout.php">Logout</a><div style="float: right; padding-top: 15px; padding-left: 15px; padding-right: 15px; vertical-align: middle;">Welcome '.$_SESSION["singedcats_fname"].'!</div>';
                }
                //else header("Location: login.html");
                ?>   
            </div>
          </div>
        </div>

      </div>
    </div>
	
<div id="main">
    
<form id="userForm" class="form-signin" role="form" action="adduser.php" method="post" onsubmit="return validate()">
    <h2 class="form-signin-heading orange">Add User</h2>
    <input type="text" class="form-control" placeholder="Username" id="username" name="username" required autofocus>
    <input type="password" class="form-control" placeholder="Password" id="password" name="password" required>
    <input type="password" class="form-control" placeholder="Confirm Password" id="passwordCheck" name="passwordCheck" required>
    <input type="text" class="form-control" placeholder="First name" id="fname" name="fname" required>
    <input type="text" class="form-control" placeholder="Last name" id="lname" name="lname" required>
    <input type="email" class="form-control" placeholder="Email address" id="email" name="email" required>
    <button class="btn btn-lg btn-danger" style="float:left; width:50%;" type="reset" name="reset" value="reset">Reset</button>
    <button class="btn btn-lg btn-danger" style="width:50%;" type="submit" name="save" value="save">Save</button>
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