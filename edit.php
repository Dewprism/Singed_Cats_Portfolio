<?php
	session_name("singedcats");
	session_start();
	
	require_once 'class/DB.php';
	$db = new DB();
	$db->connect();
	
	//Check login
	if(!isset($_SESSION["singedcats_loggedIn"])) {
        header("Location: login.php");
	}

	//DELETE
	//If user has passed a userID, and we're not editing or saving... assume deletion.
	if (isset($_POST["artID"]) && !isset($_POST["edit"]) && !isset($_POST["save"])) {
		$e = "fail";
		$file = $db->selectSome("filename", "art","id='".$_POST["artID"]."'");
		$file = "images/".$_SESSION["singedcats_username"]."/".$file["filename"];
		if ($db->delete("art_tags","artID = '".$_POST["artID"]."'")) {
			if($db->delete("art","id = '".$_POST["artID"]."'")) $e = "success";
		};
		if (unlink($file)) $e = "success";
		else $e = "fail";
		header("Location: gallery.php?artist=".$_SESSION['singedcats_userID']."&e=$e");
	}
	
	//SAVE
	if (isset($_POST["save"])) {
		$e = "fail";
		$artupdate = array("category"=>"'".$_POST["category"]."'", "description"=>"'".mysql_real_escape_string($_POST["description"])."'");
		if ($db->update($artupdate, "art", "id = '".$_POST["artID"]."'")) $e = "success";

		if($db->delete("art_tags","artID = '".$_POST["artID"]."'")) {
			$tagmax = $db->selectCount("id","tags");
			for ($i = 0; $i < $tagmax; $i++) {
				if (isset($_POST[$i])) {
					$tag = array("artID"=>$_POST["artID"],"tagID"=>$i);
					if ($db->insert($tag, "art_tags")) $e = "success";
				}
			}
		}
		header("Location: gallery.php?artist=".$_SESSION['singedcats_userID']."&e=$e");
	}
	
	//EDIT--v
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Singed Cat Studios : Gallery</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/upload.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	<link href="css/override.css" rel="stylesheet">
	
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
				<li class="dropdown active">
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
	
	<?php
		if (isset($_POST["edit"])) {
			$art = $db->selectAll("art","id='".$_POST["artID"]."'");
		}
//Edit form	
echo '<div id="upload">';

	echo '<form id="submission" role="form" action="edit.php" method="post" onsubmit="return validate()">';
		echo '<div class="section" style="text-align:center;">';
		echo '<img style="width:100%;" src="images/'.$_SESSION["singedcats_username"].'/'.$art["filename"].'">';	
		echo '</div>';
		echo '<div class="section form-group">';
			echo '<h2 class="red">Category</h2>';
			echo '<label><input type="radio" name="category" value="Original"';
			if ($art["category"] == "Original") {
				echo 'checked';
			}
			echo '> Original</label><br>';
			echo '<label><input type="radio" name="category" value="Fanart"';
			if ($art["category"] == "Fanart") {
				echo 'checked';
			}
			echo '> Fanart</label>';
		echo '</div>';
		echo '<div class="section">';
			echo '<h2 class="orange">Tags</h2>';
				$arttag_table = $db->selectSome("tagID","art_tags","artID = ".$_POST['artID']);
				$arttags = array();
				foreach ($arttag_table as $row) {
					array_push($arttags, $row["tagID"]);
				}
				$tags = $db->selectAll("tags");
                //Split tags into pretty little columns
				$column[0] = floor(count($tags)/3);
				$column[1] = floor(count($tags)/3);
				$column[2] = floor(count($tags)/3);
				for ($i = 0; array_sum($column) < count($tags); $i++) {
					$column[$i]++;
				}
				echo '<div id="left" class="column">';
				for ($i = 0; $i < $column[0]; $i++) {
					echo '<label><input type="checkbox" name="'.$tags[$i]['id'].'" value="'.$tags[$i]['tag'].'"';
					if (in_array($tags[$i]['id'], $arttags)) echo 'checked';
					echo '> '.$tags[$i]['tag'].'</label><br>';
				}
				echo '</div>';
				echo '<div id="center" class="column">';
				for ($i = $column[0]; $i < $column[0] + $column[1]; $i++) {
					echo '<label><input type="checkbox" name="'.$tags[$i]['id'].'" value="'.$tags[$i]['tag'].'"';
					if (in_array($tags[$i]['id'], $arttags)) echo 'checked';
					echo '> '.$tags[$i]['tag'].'</label><br>';
				}
				echo '</div>';
				echo '<div id="right" class="column">';
				for ($i = $column[0] + $column[1]; $i < $column[0] + $column[1] + $column[2]; $i++) {
					echo '<label><input type="checkbox" name="'.$tags[$i]['id'].'" value="'.$tags[$i]['tag'].'"';
					if (in_array($tags[$i]['id'], $arttags)) echo 'checked';
					echo '> '.$tags[$i]['tag'].'</label><br>';
				}
				echo '</div>';

		echo '</div>';
		echo '<div class="section">';
				echo '<h2 class="yellow">Description</h2>';
				echo '<textarea class="form-control" rows="5" style="width:98%;" id="description" name="description" placeholder="Please enter a brief description of the work.">'.$art['description'].'</textarea>';
		echo '</div>';
		echo '<input class="btn btn-default" style="margin:20px; margin-top:0px;" type="submit" name="save" value="Save">';
		echo '<input class="btn btn-default" style="margin:20px; margin-top:0px; margin-right:0px;" type="reset" value="Reset">';
		echo '<input type="hidden" name="artID" value='.$_POST["artID"].'>';
	echo '</form>';
	
echo '</div>';
?>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>