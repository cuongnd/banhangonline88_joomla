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

class PhpException extends ErrorException {

     function __construct($errstr, $errno, $errfile, $errline) {
         parent::__construct($errstr, 0, $errno, $errfile, $errline);
     }

}

class PhpError extends PhpException {
    /*
     * Must change the error message for undefined variables
     * Otherwise, Exceptional groups all errors together (regardless of variable name)
     */
    function __construct($errstr, $errno, $errfile, $errline) {
        if (@substr($errstr, 0, 25) == "Call to undefined method ") {
            $errstr = substr($errstr, 25)." is undefined";
        }
        parent::__construct($errstr, $errno, $errfile, $errline);
    }

}

class PhpWarning extends PhpException {
}

class PhpStrict extends PhpException {
}

class PhpParse extends PhpException {
}

class PhpNotice extends PhpException {
    /*
     * Must change the error message for undefined variables
     * Otherwise, Exceptional groups all errors together (regardless of variable name)
     */
    function __construct($errstr, $errno, $errfile, $errline) {
        if (@substr($errstr, 0, 20) == "Undefined variable: ") {
            $errstr = "\$".substr($errstr, 20)." is undefined";
        }
        parent::__construct($errstr, $errno, $errfile, $errline);
    }

}
