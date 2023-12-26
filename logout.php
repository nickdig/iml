<?php

//logout.php

include('config.php');

//Reset OAuth access token
$google_client->revokeToken();

session_destroy();

//redirect page to Home.php
header('location: index.php');

?>