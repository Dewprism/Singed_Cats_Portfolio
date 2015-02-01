<?php
    session_name("singedcats");
    session_start();

	//Check login
	if(!isset($_SESSION["singedcats_loggedIn"])) {
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
    <title>Singed Cat Studios : Upload</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom styles for this template -->
	<link href="css/override.css" rel="stylesheet">
	<link href="css/upload.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script>
        function validate() {

        }
    </script>
  </head>
  <body>
<!-- Navbar -->
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
                <li class="active"><a href="upload.php">Upload</a></li>
                <li><a href="about.php">About</a></li>
				<?php
					if ($_SESSION["singedcats_username"] == "dwallace") {
						echo '<li><a href="admin.php">Admin</a></li>';
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
<!-- /Navbar -->

<!-- Upload form -->
<div id="upload">

		<form id="submission" role="form" action="upload_file.php" method="post" enctype="multipart/form-data" onsubmit="return validate()">
			<?php 
				if (isset($_GET["e"])) {
					if ($_GET["e"] == "success") $text = "File uploaded successfully.";
					else $text = "Upload failed.";
					echo '<p class="red" style="margin-top:30px;">'.$text.'</p>';
				}
			?>
			<div class="section form-group">
				<h2 class="red">File Upload</h2>
				<input type="file" accept="image/*" id="file" name="file">
				<p class="help-block">jpg, png, or gif file formats only.</p>
			</div>
			<div class="section form-group">
				<h2 class="orange">Category</h2>
				<label><input type="radio" name="category" value="Original" checked> Original</label><br>
				<label><input type="radio" name="category" value="Fanart"> Fanart</label>
			</div>
			<div class="section">
				<h2 class="yellow">Tags</h2>
                <?php
                    $tags = $db->selectAll("tags");
                    $column[0] = floor(count($tags)/3);
                    $column[1] = floor(count($tags)/3);
                    $column[2] = floor(count($tags)/3);
                    for ($i = 0; array_sum($column) < count($tags); $i++) {
                        $column[$i]++;
                    }
                    echo '<div id="left" class="column">';
                    for ($i = 0; $i < $column[0]; $i++) {
                        echo '<label><input type="checkbox" name="'.$tags[$i]['id'].'" value="'.$tags[$i]['tag'].'"> '.$tags[$i]['tag'].'</label><br>';
                    }
                    echo '</div>';
                    echo '<div id="center" class="column">';
                    for ($i = $column[0]; $i < $column[0] + $column[1]; $i++) {
                        echo '<label><input type="checkbox" name="'.$tags[$i]['id'].'" value="'.$tags[$i]['tag'].'"> '.$tags[$i]['tag'].'</label><br>';
                    }
                    echo '</div>';
                    echo '<div id="right" class="column">';
                    for ($i = $column[0] + $column[1]; $i < $column[0] + $column[1] + $column[2]; $i++) {
                        echo '<label><input type="checkbox" name="'.$tags[$i]['id'].'" value="'.$tags[$i]['tag'].'"> '.$tags[$i]['tag'].'</label><br>';
                    }
                    echo '</div>';
                ?>
			</div>
			<div class="section">
					<h2 class="red">Description</h2>
					<textarea class="form-control" rows="5" style="width:98%;" id="description" name="description" placeholder="Please enter a brief description of the work."></textarea>
			</div>
			<input class="btn btn-default" style="margin:20px; margin-top:0px;" type="submit" value="Submit">
			<input class="btn btn-default" style="margin:20px; margin-top:0px; margin-right:0px;" type="reset" value="Reset">
		</form>
	
</div>
<!-- /Upload form-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>