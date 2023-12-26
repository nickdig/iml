<?php
/*

		Instructional Modeling Language
			
			info.php: script to add user information after they authorize accounts for IML use 


*/


require('credentials.php');


//creating the initial mysqli connection to the Database with provided credentials 
$mysqli = mysqli_connect($host, $user,$pass,$db);

if (mysqli_connect_errno($mysqli)) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	die;

}

/*
 
 Adds a new user to the iml database
 
 @param $email - the user's email 
 @param $first_name - the user's first name
 @param $last_name - the user's last name
 @param $picture - the user's picture 
 
 @return message based on if the add was successful 


*/
function addNewUser($email, $first_name, $last_name, $picture){
	global $mysqli;


	// if the user is already in the table we will update their last_login time
	if(getUserId($email) != null ){
		
		$sql ='update user set last_login = NOW() where email =?';
		$stmt = $mysqli->prepare($sql);
		
		if (!$stmt) {
			error_log("Error on updating last login  " . $mysqli->error);
	
			return null;
	    }
		
		$stmt->bind_param("s",$email);
		$stmt->execute();
		
		//error_log('$email has logged in.');
		
		return 'Not new User. Last Login has been updated.';
	}
	else{
		
		$stmt = $mysqli->prepare("INSERT INTO user (userId,first_name,last_name, email, picture, last_login) values (UUID(),?,?,?,?,NOW())");

		if (!$stmt) {
			error_log("error on add " . $mysqli->error);
			return "error";
		}

		$stmt->bind_param("ssss",$first_name,$last_name,$email,$picture);
		$stmt->execute();
	
	
	
		//error_log('$email has been added to the database');
	
		return "New User Added";
	}	
}

/*

Returns the userId based on the provided email. If there is no userId a null will be returned 

@param $email - the user's email address 

@return $userId - the userId for the provided email address 
*/
function getUserId($email){
	global $mysqli;
	
	$stmt = $mysqli->prepare("select userId from user where email=?");
	if (!$stmt) {
		error_log("Error on getValue " . $mysqli->error);
		return null;
	}


	$stmt->bind_param('s',$email);
	$stmt->execute();
	$stmt->bind_result($userId);
	$stmt->fetch();
	
	return $userId;
}



/*

Returns the userType based on the provided email. If there is no userType a null will be returned 

@param $email - the user's email address 

@return $userType - the userType for the provided email address 
*/


function getUserType($email){
	global $mysqli;
	
	$stmt = $mysqli->prepare("select userType from user where email=?");
	if (!$stmt) {
		error_log("Error on getValue " . $mysqli->error);
		return null;
	}


	$stmt->bind_param('s',$email);
	$stmt->execute();
	$stmt->bind_result($userType);
	$stmt->fetch();
	
	
	return $userType ;

}


/*

Sets the users session variable to their type

@param $type - the inputed user type  
@param $email - the user's email address 
*/


function addUserType($type,$email){
	global $mysqli;
	
	$stmt = $mysqli->prepare("Update user set userType = ? where email=?");
	if (!$stmt) {
		error_log("Error on getValue " . $mysqli->error);
		return "Error seting the users type.";
	}
	
	
	$stmt->bind_param('ds',$type,$email);
	$stmt->execute();
	
	
	$_SESSION['position'] = $type;
	

}



/*

Sets the users session variable after login to their data from the db

@param $email - the user's email address 
*/
function setUsersData($email){
	global $mysqli;
	
	$stmt = $mysqli->prepare("select first_name, last_name, email, picture from user where email=?");
	if (!$stmt) {
		error_log("Error on getValue " . $mysqli->error);
		return "Error has occurred setting users data.";
	}
	
	$stmt->bind_param('s',$email);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	
	$_SESSION['picture'] = $row['picture'];
	$_SESSION['user_first_name'] = $row['first_name'];
	$_SESSION['user_last_name'] = $row['last_name'];
	$_SESSION['user_email_address'] = $email;
	
}



?>