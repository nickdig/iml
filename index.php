<?php
require 'scripts/encrypt_decrypt.php';
require 'config.php';
require 'info.php';

$login_button = '';

if (isset($_REQUEST['level'])) {

    addUserType($_REQUEST['level'], $_SESSION['user_email_address']);

}

//This $_GET["code"] variable value received after user has login into their Google Account redirct to PHP script then this variable value has been received
if (isset($_GET["code"])) {
    //It will Attempt to exchange a code for an valid authentication token.
    $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);

    //This condition will check there is any error occur during geting authentication token. If there is no any error occur then it will execute if block of code/
    if (!isset($token['error'])) {
        //Set the access token used for requests
        $google_client->setAccessToken($token['access_token']);

        //Store "access_token" value in $_SESSION variable for future use.
        $_SESSION['access_token'] = $token['access_token'];

        //Create Object of Google Service OAuth 2 class
        $google_service = new Google_Service_Oauth2($google_client);

        //Get user profile data from google
        $data = $google_service->userinfo->get();

        //try to add the user to the DB. Will successfully add if they are a new sign in
        if (!empty($data['given_name']) && !empty($data['family_name']) && !empty($data['email']) && !empty($data['picture'])) {

            $email = htmlspecialchars($data['email']);
            $first_name = htmlspecialchars($data['given_name']);
            $last_name = htmlspecialchars($data['family_name']);
            $picture = htmlspecialchars($data['picture']);

            addNewUser($email, $first_name, $last_name, $picture);

            setUsersData($email);

        }
    }
}

if (isset($_SESSION['user_email_address'])) {

    $position = getUserType($_SESSION['user_email_address']);

    if (!isset($_SESSION['position']) && $position == null) {

        $positionPrompt = '<div class="modal show"role="dialog">
							<div class="modal-dialog modal-lg">
								<div class="modal-content">
									<div class="modal-header">
									<h4 class="modal-title">What is your role in learning modeling with IML?</h4>
									</div>
									<div class="modal-body">
									<form action="index.php" method="POST">
									<input type="radio" id="Student" name="level" value=1>
									<label for="Student">Student in Course</label><br>
									<input type="radio" id="Professor" name="level" value=2>
									<label for="Professor">Course Instructor</label><br>
									<input type="radio" id="Casual" name="level" value=3>
									<label for="Casual">Casual User</label><br>
									<button type="submit" class="btn btn-default" data-dismiss="modal">Submit</button>
									</div>
								 </form>
							</div>
						</div>';

    } else {

        $_SESSION['position'] = $position;

    }
}
//This is for check user has login into system by using Google account, if User not login into system then it will execute if block of code and make code for display Login link for Login using Google account.
if (!isset($_SESSION['access_token'])) {
    //Create a URL to obtain user authorization
    $login_button = '<a href="' . $google_client->createAuthUrl() . '"><img class="login-btn" src="images/btn_google_signin_dark_normal_web.png" /></a>';

}

if(isset($_SESSION['structModelLoaded'])){
  unset($_SESSION['structModelLoaded']);
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Home IML</title>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous">
        </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-PmY9l28YgO4JwMKbTvgaS7XNZJ30MK9FAZjjzXtlqyZCqBY6X6bXIkM++IkyinN+" crossorigin="anonymous">
    <link href="css/Home.css" rel="stylesheet">
    <script src="scripts\home.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<body>
    <nav class="navbar navbar-default HomeNavBar" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">IML</a>
            </div>
            <div class="collapse navbar-collapse" id ="navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="">Examples</a></li>
                    <li class="active"><a href="">Discussion Forum</a></li>
                    <li class="active"><a href="">.... </a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                            aria-expanded="false"><?php if ($login_button == '') {
    print 'Profile';
} else {
    print 'Login';
}
?> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
							<?php
if ($login_button == '') {
    print '<li class="dropdown-header"><p><h4>Profile Information</h4></p></li>';
    print '<li role ="presentation" class="divider"></li>';
    print '<li role="presentation"><p class ="navbar-text"><b>Name :</b><br>' . $_SESSION['user_first_name'] . ' ' . $_SESSION['user_last_name'] . '</p></li>';
    print '<li role="presentation"><p class="navbar-text"><b>Email :</b><br>' . $_SESSION['user_email_address'] . '</p></li>';
    print '<li role="presentation"><a class="text-center" role="menuitem" href="editAccount.php">Edit Account</a></li>';
    print '<li role="presentation"><a class="text-center" role="menuitem" href="logout.php">Logout</a></li>';
} else {
    print '<div align="center">' . $login_button . '</div>';
}
?>
                        </ul>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>


<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
 <div class="panel panel-default IML-header">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
	  <p class="text-left text-justify  IML-header-text-title ">Instructional Modeling Language </p>
	</div>
	<div class="text-justify col-lg-3 col-lg-offset-3 col-md-3 col-md-offset-3 col-sm-3 col-sm-offset-3 col-xs-3 col-xs-offset-3">
	   <p class="text-right">Click any of the banners below to explore IML's features and begin your own IML projects.</p>
	</div>

   </div>


 <div class="panel panel-default IML-panel-structural " role="button" href="#collapseOne" data-toggle="collapse"  data-parent="#accordion"  aria-expanded="false" aria-controls="collapseOne" id="headingOne">
   <div class="text-center col-lg-3 col-md-3 col-sm-3 col-xs-3">
	<img src="images/structuralmodeling.png" alt="structural modeling picture" class="img-fluid Home-btn-image">
	</div>
	 <div class="text-center IML-panel-title col-lg-6 col-md-6 col-sm-6 col-xs-6">
	  <h2>
		Structural Modeling
      </h2>
	  </div>

	</div>
    <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">

	  <div class ="col-xs-12 col-sm-12 col-md-6 col-lg-6">
	  <div class="alert alert-info" role="alert">
		 <p class="text-justify">
		<span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
		Use Structural Modeling to describe the structure of your system including objects, attributes, and relations. From here you can generate Java code representations of your System.
	  </p>
	  </div>


	  <div class="list-group">
		<a href="structural.php" class="list-group-item">Create a New Structural Modeling Project</a>
		<a href="#" class="list-group-item" data-toggle="modal" data-target="#structModal">Continue an Existing Structural Modeling Project</a>
		<a href="#" class="list-group-item">View Structural Modeling Tutorial</a>
		<a href="#" class="list-group-item">Browse Example Structural Models</a>
	</div>

	  </div>

<div class="modal fade" id="structModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="myModalLabel">Existing Structural Projects </h3>
      </div>
      <div class="modal-body">
      <form id="structuralModelForm" action="structural.php" method ="GET" autocomplete="off">
      <select id="project" name="project" onchange="enablebtn()" size="5" style="min-width:100%;">
        <option value="<?php echo encrypt_decrypt('encrypt',"/var/www/gerardnt/iml/structural/University.iml"); ?>">University</option>
        <option value="<?php echo encrypt_decrypt('encrypt',"/var/www/gerardnt/iml/structural/Car.iml"); ?>">Car</option>
	      <option value="<?php echo encrypt_decrypt('encrypt',"/var/www/gerardnt/iml/structural/Hi_Nick.iml"); ?>">HiNick</option>
        </select>
      </form> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="openStructural" class="btn btn-primary" disabled>Open Project</button>
      </div>
    </div>
  </div>
</div>
	  
	  <div class ="col-xs-12 col-sm-12 col-md-6 col-lg-6 text-center">
	  <img src="images/structural.gif" class="IML-gif-border img-responsive" alt="structural project example gif">
	  </div>
	 </div>
    </div>


 <div class="panel panel-default IML-panel-transform" id="headingTwo" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" >

	<div class="text-center col-lg-3 col-md-3 col-sm-3 col-xs-3">
	<img src="images/modeltransformation.png" alt="model transformation image" class="Home-btn-image img-fluid">
	</div>
	 <div class="text-center IML-panel-title col-lg-6 col-md-6 col-sm-6 col-xs-6">
	  <h2>
		Model Transformations
      </h2>
	  </div>

	</div>

    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
      <div class="panel-body">
		<div class ="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		  <div class="alert alert-info" role="alert">
			 <p class="text-justify">
			<span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
			Use Model Transformations to map and convert from one model type to another. You will provide source and target meta-models, as well as create rules, all to convert your input models to resulting output models.
		  </p>
		  </div>


		  <div class="list-group">
			<a href="model-transformation.html" class="list-group-item">Create a New Model Transformation</a>
			<a href="#" class="list-group-item">Continue an Existing Modeling Transformation</a>
			<a href="#" class="list-group-item">View Model Transformation Tutorial</a>
			<a href="#" class="list-group-item">Browse Example Model Transformations</a>
		</div>

		  </div>

		  <div class ="col-xs-12 col-sm-12 col-md-6 col-lg-6 text-center">
		  <img src="images/structural.gif" class="IML-gif-border img-responsive" alt="structural project example gif">
		  </div>

		 </div>
	</div>



  <div class="panel panel-default IML-panel-model" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree" id="headingThree" >

	 <div class="text-center col-lg-3 col-md-3 col-sm-3 col-xs-3">
	<img src="images/sourceCode.png" alt="source code image" class="Home-btn-image img-fluid">
	</div>
	 <div class="text-center IML-panel-title col-lg-6 col-md-6 col-sm-6 col-xs-6">
	  <h2>
		Source Code Editing
      </h2>
	  </div>

	</div>

    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
      <div class="panel-body">
		<div class ="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		  <div class="alert alert-info" role="alert">
			 <p class="text-justify">
			<span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
			Generated code from your models behaves just like regular Java code. Use this editor to interact with the generated code, compile, and run it as you would in your own IDE.
		  </p>
		  </div>


		  <div class="list-group">
			<a href="codeIDE.html" class="list-group-item">Create a New Source Code Project</a>
			<a href="#" class="list-group-item">Continue an Existing Source Code Project</a>
			<a href="#" class="list-group-item">View Model Source Code Tutorial</a>
			<a href="#" class="list-group-item">Browse Example Source Code Projects</a>
		</div>

		  </div>

		  <div class ="col-xs-12 col-sm-12 col-md-6 col-lg-6 text-center">
		  <img src="images/structural.gif" class="IML-gif-border img-responsive" alt="structural project example gif">
		  </div>

	   </div>
    </div>





   <div class="panel panel-default IML-panel-test" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour" id="headingFour">

    <div class="text-center col-lg-3 col-md-3 col-sm-3 col-xs-3">
	<img src="images/behavioralmodeling.png" alt="behavioral modeling picture" class ="Home-btn-image img-fluid" >
	</div>
	 <div class="text-center IML-panel-title col-lg-6 col-md-6 col-sm-6 col-xs-6">
	  <h2>
		Behavioral Modeling
      </h2>
	  </div>


   </diV>

    <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
      <div class="panel-body">

	   </div>
    </div>


   <div class="panel panel-default IML-panel-edit" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive" id="headingFive">


    <div class="text-center col-lg-3 col-md-3 col-sm-3 col-xs-3">
	<img src="images/modelbasedTesting.png" alt="model based testing image" class="Home-btn-image img-fluid">
	</div>
	 <div class="text-center IML-panel-title col-lg-6 col-md-6 col-sm-6 col-xs-6">
	  <h2>
		Model Based Testing
      </h2>
	  </div>



	</div>
    <div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFive">
      <div class="panel-body">

	   </div>
    </div>
  </div>

</div>







<?php

if (isset($positionPrompt)) {
    print $positionPrompt;
}

?>


</body>

</html>