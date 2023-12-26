<?php

//config.php

//Include Google Client Library for PHP autoload file
require_once '/vendor/google/auth/autoload.php';
//Make object of Google API Client for call Google API
$google_client = new Google_Client();

//Set the OAuth 2.0 Client ID
$google_client->setClientId('907852305108-7vjt09b61qoqjajfu2upc6eccf6fkn06.apps.googleusercontent.com');

//Set the OAuth 2.0 Client Secret key
$google_client->setClientSecret('B5mO8N-xU7-Y4eADoxQpRVb_');

//Set the OAuth 2.0 Redirect URI
$google_client->setRedirectUri('localhost://iml.cec.miamioh.edu/');

//
$google_client->addScope('email');

$google_client->addScope('profile');

session_start();



?>
