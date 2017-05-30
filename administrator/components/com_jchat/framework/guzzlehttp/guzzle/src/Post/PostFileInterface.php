<?php
namespace GuzzleHttp\Post;
/**
 *
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage guzzlehttp
 * @subpackage guzzle
 * @subpackage Post
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
use GuzzleHttp\Stream\StreamInterface;

/**
 * Post file upload interface
 */
interface PostFileInterface
{
    /**
     * Get the name of the form field
     *
     * @return string
     */
    public function getName();

    /**
     * Get the full path to the file
     *
     * @return string
     */
    public function getFilename();

    /**
     * Get the content
     *
     * @return StreamInterface
     */
    public function getContent();

    /**
     * Gets all POST file headers.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is a string.
     *
     * @return array Returns an associative array of the file's headers.
     */
    public function getHeaders();
}
