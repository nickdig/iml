<?php
session_start();

require("info.php");

if(!isset($_SESSION['user_email_address'])){
	header('Location: index.php');
}

if(isset($_REQUEST['cancel'])){
	header('Location: index.php');
}

if(!isset($_SESSION['position'])){
	 header('Location: index.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Account - IML</title>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous">
        </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
	<script src="scripts/editProfile.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-PmY9l28YgO4JwMKbTvgaS7XNZJ30MK9FAZjjzXtlqyZCqBY6X6bXIkM++IkyinN+" crossorigin="anonymous">
    <link href="css/Home.css" rel="stylesheet">
	 <link href="css/editAccount.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-default HomeNavBar">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#navbar-collapse" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">IML</a>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="index.php">Home<span class="sr-only">(current)</span></a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                            aria-expanded="false"><?php print 'Profile';?> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
							<?php
	print '<li class="dropdown-header"><p><h4>Profile Information</h4></p></li>';
	print '<li role ="presentation" class="divider"></li>';
    print '<li role="presentation"><p class ="navbar-text" id="UsersFirstName"><b>Name :</b><br>'.$_SESSION['user_first_name'].' '.$_SESSION['user_last_name']. '</p></li>';
    print '<li role="presentation"><p class="navbar-text"><b>Email :</b><br>'.$_SESSION['user_email_address'].'</p></li>';
	print '<li role="presentation"><a class="text-center" role="menuitem" href="editAccount.php">Edit Account</a></li>';
	print '<li role="presentation"><a class="text-center" role="menuitem" href="logout.php">Logout</a></li>';
  
   ?>
                        </ul>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>

<div class="container">
<div id="msg-div"></div>
<div class="center-block">
<img id="UserImage" src="<?php print  $_SESSION['picture']; ?>" alt="Current User Profile Picture" class="img-circle center-block" width="125" height ="125">
<form id ="edit-profile" action ="scripts/UpdateAccount.php" method = "POST" enctype ="multipart/form-data">
  <div class="form-group">
  <label for="image">Profile Picture</label>
  <input type="file" class="form-control-file" name="image"  id="image" accept ="image/*">
  </div>
  <div class="form-group">
    <label for="email">Email address</label>
    <input type="email" class="form-control" id="email" placeholder=<?php print $_SESSION['user_email_address']; ?> readonly>
  </div>
  <div class="form-group ">
    <label for="first_name">First Name</label>
    <input type="text" class="form-control" id="first_name" name= "first_name" placeholder=<?php print $_SESSION['user_first_name']; ?>  value="<?php print $_SESSION['user_first_name']; ?>">
  </div>
   <div class="form-group">
    <label for="last_name">Last Name</label>
    <input type="text" class="form-control" id="last_name"  name= "last_name" placeholder=<?php print $_SESSION['user_last_name']; ?> value="<?php print $_SESSION['user_last_name']; ?>"  >
  </div>
 <div class="form-group">
  <label for="userType">Type of User</label>
	<?php

		if($_SESSION['position'] == 2){
			$type ="Professor";
		}
		else if ($_SESSION['position'] == 1){
			$type ="Student";
		}
		else{
			$type ="Casual";
		}
		
		print '<input type="text" class="form-control" id="userType" name= "userType" placeholder= '.$type.' readonly>';
 
	?>
 
</div>

  <button type="submit" class="btn btn-primary form-submit-btn" >Update Profile</button>
  <a href=?cancel><button type="button" class="btn btn-primary form-submit-btn ">Cancel</button></a>
</form>

</div>

</body>
</html>