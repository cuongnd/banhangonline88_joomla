<?php
// namespace administrator\components\com_jchat\framework;
/**
 *
 * @package JCHAT::CONFIG::administrator::components::com_jchat
 * @subpackage framework
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace OAuth\Common\Http\Uri;
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Factory interface for uniform resource indicators
 */
interface UriFactoryInterface
{
    /**
     * Factory method to build a URI from a super-global $_SERVER array.
     *
     * @param array $_server
     *
     * @return UriInterface
     */
    public function createFromSuperGlobalArray(array $_server);

    /**
     * Creates a URI from an absolute URI
     *
     * @param string $absoluteUri
     *
     * @return UriInterface
     */
    public function createFromAbsolute($absoluteUri);

    /**
     * Factory method to build a URI from parts
     *
     * @param string $scheme
     * @param string $userInfo
     * @param string $host
     * @param string $port
     * @param string $path
     * @param string $query
     * @param string $fragment
     *
     * @return UriInterface
     */
    public function createFromParts($scheme, $userInfo, $host, $port, $path = '', $query = '', $fragment = '');
}
