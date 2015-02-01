<?php
    //Start session, db connection
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
    <title>Singed Cat Studios : Gallery</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <!-- Slimbox CSS -->
    <link rel="stylesheet" href="css/slimbox2.css" type="text/css" media="screen" />
      
    <!-- Custom CSS-->
	<link href="css/override.css" rel="stylesheet">
	
	<script>
    var category;
    var tags;
    //Nonfunctional AJAX that stumped me; stripped the contents until I figure it out
	function cull() {
        var xmlhttp;
        if (window.XMLHttpRequest) {
            xmlhttp = new XMLHttpRequest();
        }
	}
	
    //Confirm deletion so someone doesn't oops
	function deleteCheck(id) {
		if (confirm("Are you sure you want to delete this image?")) {
			document.forms["form" + id].submit();
		}
	}
	</script>
	
  </head>
<body>
<!-- Navbar-->
      <!-- Ultimately it would be nice to access this from a universal navbar.php file but for now we'll leave it inline-->
      <!-- ?php include 'navbar.php';?-->
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
<!-- /Navbar-->


<div id="main" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

<!-- Left menu-->
<div class="container col-lg-2 col-md-2 col-sm-3 col-xs-12">
	<?php 
		if (isset($_GET["e"])) {
			if ($_GET["e"] == "success") $text = "Changes saved.";
			else $text = "Save failed.";
			echo '<p class="red">'.$text.'</p>';
		}
	?>
	<form>
		<h3 class="orange">Category</h3>
		<?php
		$categories = $db->selectSome("distinct category","art");
		foreach ($categories as $category) {
			echo '<label><input onclick="cull();" type="radio" class="category" name="category" value="'.$category['category'].'"> '.$category['category'].'</label><br>';
		}
		?>
		<h3 class="red">Tags</h3>
		<?php
		$tags = $db->selectAll("tags");
		foreach ($tags as $tag) {
		 echo '<label><input onclick="cull();" type="checkbox" class="tag" id="'.$tag['id'].'" name="'.$tag['id'].'" value="'.$tag['tag'].'"> '.$tag['tag'].'</label><br>';
		}
		$tagcount = $db->selectCount("id", "tags");
        echo '<input type="hidden" id="tagcount" name="tagcount" value="'.$tagcount.'">';
        ?>
	</form>
</div>
<!-- /Left Menu-->

<!-- Gallery-->
<div class="container col-lg-10 col-md-10 col-sm-9 col-xs-12">
	<div id="js-masonry" class="row">
		<?php
		//If they're logged in and viewing their own gallery, they can edit it
		if (isset($_GET['artist']) && isset($_SESSION['singedcats_userID']) && ($_GET['artist'] == $_SESSION['singedcats_userID'])) {
			$art = $db->selectSome("art.id as id, artistID, username, filename, description","art inner join users on artistID = users.id","artistID = ".$_GET['artist'],"art.id desc");
            //If there's only one result, the array returns differently so this handles that
			if (isset($art['id'])) {
                $art['description'] = str_replace('"', '&quot;',$art['description']);
                $art['description'] = str_replace('\\', '',$art['description']);
				echo '<div class="item col-xs-12 col-sm-6 col-md-4 col-lg-3">';
				echo '<form id="form'.$art['id'].'" class="editart" action="edit.php" method="POST">';
                echo '<input type="hidden" name="artID" value='.$art['id'].'>';
                echo '<input class="btn btn-sm btn-default" style="margin-right:3px;" type="submit" name="edit" value="Edit">';
				echo '<button id="button" type="button" onclick="deleteCheck('.$art['id'].');" class="btn btn-sm btn-default" name="delete" value="delete">Delete</button>';
				echo '</form>';
				echo '<a href="images/'.$art['username'].'/'.$art['filename'].'" class="thumbnail" rel="lightbox-gallery" title="'.$art['description'].'">';
				echo '<img src="images/'.$art['username'].'/'.$art['filename'].'" alt="'.$art['description'].'">';
				echo '</a>';
				echo '</div>';
			}
            //If there's more than one do a foreach
			else {
				foreach ($art as $art) {
                    $art['description'] = str_replace('"', '&quot;',$art['description']);
                    $art['description'] = str_replace('\\', '',$art['description']);
					echo '<div class="item col-xs-12 col-sm-6 col-md-4 col-lg-3">';
					echo '<form id="form'.$art['id'].'" class="editart" action="edit.php" method="POST">';
					echo '<input type="hidden" name="artID" value='.$art['id'].'>';
					echo '<input class="btn btn-sm btn-default" style="margin-right:3px;" type="submit" name="edit" value="Edit">';
					echo '<button id="button" type="button" onclick="deleteCheck('.$art['id'].');" class="btn btn-sm btn-default" name="delete" value="delete">Delete</button>';
					echo '</form>';
					echo '<a href="images/'.$art['username'].'/'.$art['filename'].'" class="thumbnail" rel="lightbox-gallery" title="'.$art['description'].'">';
					echo '<img src="images/'.$art['username'].'/'.$art['filename'].'" alt="'.$art['description'].'">';
					echo '</a>';
					echo '</div>';
				}
			}
		}
		//If they're not logged in or it's not their gallery, they can look but not touch
		else {
			if (isset($_GET['artist'])) {
				$art = $db->selectSome("art.id as id, artistID, username, filename, description","art inner join users on artistID = users.id","artistID = ".$_GET['artist'],"art.id desc");
			}
			else {
				$art = $db->selectSome("art.id as id, artistID, username, filename, description","art inner join users on artistID = users.id","","art.id desc");
			}
			//If there's only one result, the array returns differently so this handles that
			if (isset($art['id'])) {
                $art['description'] = str_replace('"', '&quot;',$art['description']);
                $art['description'] = str_replace('\\', '',$art['description']);
				echo '<div class="item col-xs-12 col-sm-6 col-md-4 col-lg-3">';
				echo '<a href="images/'.$art['username'].'/'.$art['filename'].'" class="thumbnail" rel="lightbox-gallery" title="'.$art['description'].'">';
				echo '<img src="images/'.$art['username'].'/'.$art['filename'].'" alt="'.$art['description'].'">';
				echo '</a>';
				echo '</div>';
			}
            //If there's more than one do a foreach
			else {
				foreach ($art as $art) {
                    $art['description'] = str_replace('"', '&quot;',$art['description']);
                    $art['description'] = str_replace('\\', '',$art['description']);
					echo '<div class="item col-xs-12 col-sm-6 col-md-4 col-lg-3">';
					echo '<a href="images/'.$art['username'].'/'.$art['filename'].'" class="thumbnail" rel="lightbox-gallery" title="'.$art['description'].'">';
					echo '<img src="images/'.$art['username'].'/'.$art['filename'].'" alt="'.$art['description'].'">';
					echo '</a>';
					echo '</div>';
				}
			}
		}
		?>
	</div>
</div>
<!-- /Gallery-->

    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
	<!-- imagesLoaded for... checking that images loaded derp-->
	<script src="js/imagesloaded.pkgd.min.js"></script>
	<!-- Masonry for image layout-->
	<script src="js/masonry.pkgd.min.js"></script>
    <!-- Slimbox for image viewing-->
    <script type="text/javascript" src="js/slimbox2.js"></script>
	<script>
		jQuery(document).ready(function($){
			var $container = $('#js-masonry').masonry();
			//Layout Masonry again after all images have loaded
			$container.imagesLoaded( function() {
				$container.masonry();
			});
		});
	</script>
	</div>
  </body>
</html>