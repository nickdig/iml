<?php

session_start();

/*

Sends JSON back to the requesting client

@param $status - if there was an error or not
@param $msg - the message to be returned to the user
@param $reult - the results from the request

@return newly created JSON object with information

 */

function sendJson($status, $msg, $result)
{
    $returnData = array();
    $returnData['status'] = $status;
    $returnData['msg'] = $msg;
    foreach ($result as $k => $v) {
        $returnData[$k] = $v;
    }

    print json_encode($returnData);
    exit;
}

//parse parts
if (isset($_SERVER['PATH_INFO'])) {
    $parts = explode("/", $_SERVER['PATH_INFO']);
    //sanitize
    for ($i = 0; $i < count($parts); $i++) {
        $parts[$i] = htmlspecialchars($parts[$i]);
    }
} else {
    $parts = array();
}

array_shift($parts); //get rid of first part of the url

$method = strtolower($_SERVER['REQUEST_METHOD']);

if ($method == "options") {
    exit;
}

/*

Compile route

-Compiles the Java code

route location

-POST Request
-host/runJava.php/compile

JSON Body IN

{
"uid":"uid",
"path":"iml/x"
}

@return Message indicating if compilation was successfull or not

 */

if ($method == "post" && sizeof($parts) == 1 && $parts[0] == "compile") {

    try {

        //get the contents from the body
        $json_str = file_get_contents('php://input');
        $jsonBody = json_decode($json_str, true);

        //grab uid and the path of the java files
        $uid = htmlspecialchars($jsonBody['uid']);
        $path = htmlspecialchars($jsonBody['path']);

        //run a temporary container that compiles the java code
        $dockerCompile = 'docker run --rm -v /var/www/' . $uid . ':/var/www/' . $uid . ' openjdk:8 javac /var/www/' . $uid . '/' . $path . '/*.java';
        $compile = shell_exec($dockerCompile . ' 2>&1');

        if ($compile) {
            if (($pos = strpos($compile, "error")) !== false) {
                $message = substr($compile, $pos);
                sendJson("FAIL", $message, "");
                exit;
            }
        }

    } catch (Exception $e) {
        error_log($e);
        $errormsg = $e->getMessage();
        sendJson("FAIL", "JSON DECODE ERROR " . $errormsg, "");
        exit;
    }

    sendJson("ok", "Compiled Successfully", "");

}

/*

Run route

-Compiles the Java code then runs it and returns
the results back to the user

route location

-POST Request
-host/runJava.php/run

JSON Body IN

{
"uid":"uid",
"mainFile":"iml.x.x"
}

@return results from running Java program

 */

if ($method == "post" && sizeof($parts) == 1 && $parts[0] == "run") {

    try {
        //get the contents from the body
        $json_str = file_get_contents('php://input');
        $jsonBody = json_decode($json_str, true);

        //grab uid, path, and the main java file
        $uid = htmlspecialchars($jsonBody['uid']);
        $mainFile = htmlspecialchars($jsonBody['mainFile']);
        $path = htmlspecialchars($jsonBody['path']);

        //re-compile the java code
        $dockerCompile = 'docker run --rm -v /var/www/' . $uid . ':/var/www/' . $uid . ' openjdk:8 javac /var/www/' . $uid . '/' . $path . '/*.java';
        $compile = shell_exec($dockerCompile . ' 2>&1');

        if ($compile) {
            if (($pos = strpos($compile, "error")) !== false) {
                $message = substr($compile, $pos);
                sendJson("FAIL", $message, "");
                exit;
            }
        }

        //run the java code and grab the output to be returned
        $dockerRun = 'docker run --rm -v /var/www/' . $uid . ':/var/www/' . $uid . ' -w /var/www/' . $uid . ' openjdk:8 java ' . $mainFile;
        $run = shell_exec($dockerRun . ' 2>&1');

    } catch (Exception $e) {
        error_log($e);
        $errormsg = $e->getMessage();
        sendJson("FAIL", "JSON DECODE ERROR " . $errormsg, "");
        exit;
    }

    sendJson("ok", $run, "");

}

header($_SERVER['SERVER_PROTOCOL'] . ' 404 Invalid URL', true, 400);
