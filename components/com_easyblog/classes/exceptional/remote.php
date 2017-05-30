<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

class ExceptionalRemote {
    /*
     * Does the actual sending of an exception
     */
    static function send_exception($exception) {
        $uniqueness_hash = $exception->uniqueness_hash();
        $hash_param = ($uniqueness_hash) ? null : "&hash={$uniqueness_hash}";
        $url = "/api/errors?api_key=".Exceptional::$api_key."&protocol_version=".Exceptional::$protocol_version.$hash_param;
        $compressed = gzencode($exception->to_json(), 1);
        self::call_remote($url, $compressed);
    }

    /*
     * Sends a POST request
     */
    static function call_remote($url, $post_data) {
        if (Exceptional::$use_ssl === true) {
            $s = fsockopen("ssl://".Exceptional::$host, 443, $errno, $errstr, 4);
        }
        else {
            $s = fsockopen(Exceptional::$host, 80, $errno, $errstr, 2);
        }

        if (!$s) {
            echo "[Error $errno] $errstr\n";
            return false;
        }

        $request  = "POST $url HTTP/1.1\r\n";
        $request .= "Host: ".Exceptional::$host."\r\n";
        $request .= "Accept: */*\r\n";
        $request .= "User-Agent: ".Exceptional::$client_name." ".Exceptional::$version."\r\n";
        $request .= "Content-Type: text/json\r\n";
        $request .= "Connection: close\r\n";
        $request .= "Content-Length: ".strlen($post_data)."\r\n\r\n";
        $request .= "$post_data\r\n";

        fwrite($s, $request);

        $response = "";
        while (!feof($s)) {
            $response .= fgets($s);
        }

        fclose($s);
    }

}
