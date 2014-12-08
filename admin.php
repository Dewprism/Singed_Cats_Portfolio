<?php
    session_name("singedcats");
    session_start();

	//Check login
	if(!isset($_SESSION["singedcats_loggedIn"]) || $_SESSION["singedcats_username"] != "dwallace") {
        header("Location: login.php");
	}

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
    <title>Singed Cat Studios : User Administration</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom styles for this template -->
	<link href="css/override.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        #users {
            margin-left: auto;
            margin-right: auto;
        }
        #users tr {
            border: solid 1px;
        }
        #users td {
            padding: 3px;   
        }
    </style>
    <script>
		function deleteCheck(userID) {
			if (confirm("Are you sure you want to delete this user?")) {
				document.forms["edituser" + userID].submit();
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

<?php
	$users = $db->selectSome("lname, fname, username, id", "users");
    array_multisort($users);
    echo '<table id="users" class="center">';
	if (isset($_GET["e"]) && $_GET["e"] == "success") echo '<tr style="border:none;"><td colspan=4 class="red" style="text-align:center;">Edit successful.</td></tr>';
	if (isset($_GET["e"]) && $_GET["e"] == "fail") echo '<tr style="border:none;"><td colspan=4 class="red" style="text-align:center;">Edit failed.</td></tr>';
    echo '<tr style="border:none;"><th colspan=4><a class="btn btn-lg btn-warning btn-block" style="margin-bottom:10px;" href="adduser.php" role="button">Add User</a></th></tr>';
        foreach ($users as $user) {
            echo "<tr><td>$user[lname]</td><td>$user[fname]</td><td>$user[username]</td>";
            echo '<td><form id="edituser'.$user["id"].'" action="edituser.php" method="POST"><input type="hidden" name="userid" value="'.$user["id"].'"><input type="hidden" name="username" value="'.$user["username"].'"><input class="btn btn-sm btn-default" style="margin-right:3px; float:left;" type="submit" name="edit" value="Edit"><button id="button" type="button" onclick="deleteCheck('.$user["id"].');" class="btn btn-sm btn-default" name="delete" value="delete"';
            if ($user['username'] == 'dwallace') echo 'disabled';
            echo '>Delete</button></form></td></tr>';
        }
    echo '</table>';
?>

</div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>