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

save individual file route

-This saves the individual input as a single java file

route location

-POST Request
-host/saveCode.php/file

JSON Body IN

{
"uid":"uid",
"path":"iml/x"
"class":"hellowWorld"
"code":"class HelloWorld { public static void main(String[] args) { System.out.println("Hello, World!"); }}"
}

@return Message indicating if the file was saved successfully or not

 */

if ($method == "post" && sizeof($parts) == 1 && $parts[0] == "file") {

    try {

        //get the contents from the body
        $json_str = file_get_contents('php://input');
        $jsonBody = json_decode($json_str, true);

        //grab uid, the path to save the file, the class name, and the code
        $uid = htmlspecialchars($jsonBody['uid']);
        $path = htmlspecialchars($jsonBody['path']);
        $class = htmlspecialchars($jsonBody['class']);
        $content = $jsonBody['code'];

        $pathtodirectory = '/var/www/' . $uid . '/' . $path . '/';
        $pathtofile = $pathtodirectory . '' . $class . '.java';

        $file = fopen($pathtofile, "w");
        if ((fwrite($file, $content) == false) && (!file_exists($pathtofile))) {
            fclose($file);
            sendJson("fail", "File Not Saved, Please Try again.", "");
        } else {
            fclose($file);
            chmod($pathtodirectory, 0755);
            sendJson("ok", "File Saved", "");
        }

    } catch (Exception $e) {
        error_log($e);
        $errormsg = $e->getMessage();
        sendJson("fail", "JSON DECODE ERROR " . $errormsg, "");
        exit;
    }

}

header($_SERVER['SERVER_PROTOCOL'] . ' 404 Invalid URL', true, 400);
