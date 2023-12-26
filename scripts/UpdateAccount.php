<?php
session_start();

require('../credentials.php');

//creating the initial mysqli connection to the Database with provided credentials 
$mysqli = mysqli_connect($host, $user,$pass,$db);

if (mysqli_connect_errno($mysqli)) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	die;

}

function sendJson($status,$msg,$result) {
	$returnData = array();
	$returnData['status'] = $status;
	$returnData['msg'] = $msg;
	foreach ($result as $k=>$v) {
		$returnData[$k] = $v;
	}

	print json_encode($returnData);
	exit;
}

function updateUser($picture, $firstName, $lastname, $email){
	global $mysqli;
	
	
	$sql ='update user set picture = ?, first_name = ?, last_name = ? where email = ?';
	$stmt = $mysqli->prepare($sql);
		
		if (!$stmt) {
			error_log("Error on updating users profile  " . $mysqli->error);
	
			sendJson("FAIL","Error Updating profile","");
	    }
		
		$stmt->bind_param("ssss",$picture,$firstName,$lastname, $email);
		$stmt->execute();
		
		$_SESSION['user_first_name'] = $firstName;
	    $_SESSION['picture'] = $picture;
		$_SESSION['user_last_name'] = $lastname;
		
		$data = array();
		$data['user_first_name'] = $firstName;
		$data['picture'] = $picture;	
		$data['user_last_name'] = $lastname;
		
		sendJson("OK","updated successfully",$data);
}

// Check if image file is a actual image or fake image

   if($_FILES['image']['name'] != ""){
	    
	$location = "/var/www/html/dev/userImages/";
	$relativeLocation = "/dev/userImages/";
	$img = $_FILES['image']['name'];
	$tmp = $_FILES['image']['tmp_name'];
	$imageFileType = strtolower(pathinfo($img,PATHINFO_EXTENSION));  
	
	//allow users to upload same image
	$final_image = rand(1000,1000000).$img;
	
	// Check file size
	if ($_FILES["image"]["size"] > 1000000) {
		sendJson ("FAIL","Sorry, your file is too large.","");
	}
		
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
		sendJson("FAIL","Sorry, only JPG, JPEG, PNG & GIF files are allowed.","");
	}
	
	$path = $location.strtolower($final_image); 
	$relativePath = $relativeLocation.strtolower($final_image);
	
	if (!move_uploaded_file($tmp, $path)) {
        sendJson("FAIL","Sorry, there was an error uploading your file.","");
    }
	   updateUser($relativePath, htmlspecialchars($_POST['first_name']),htmlspecialchars($_POST['last_name']), $_SESSION['user_email_address']);
   }
   else{
	   
	   updateUser($_SESSION['picture'], htmlspecialchars($_POST['first_name']),htmlspecialchars($_POST['last_name']), $_SESSION['user_email_address']);
	   
   }
	

?>