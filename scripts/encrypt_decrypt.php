<?php

/*

encrypts or decrypts the provided string 

@param $action - either encrypt ofr decrypt
@param $string - the string you would like to perform the action on

@return the decrypted or encrypted string 

 */

function encrypt_decrypt($action, $string) 
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'a5942f846eed6547b13e43006e89eacef08aa0333f1e43a7fab386ecc5a7dde5';
        $secret_iv = 'd1cd8eff5b1a0a14a5f6d4a2c300516d';
        // hash
        $key = hash('sha256', $secret_key);    
        // iv - encrypt method AES-256-CBC expects 16 bytes 
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }



?>
