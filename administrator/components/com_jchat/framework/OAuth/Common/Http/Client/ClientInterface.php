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
namespace OAuth\Common\Http\Client;
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Http\Exception\TokenResponseException;

/**
 * Any HTTP clients to be used with the library should implement this interface.
 */
interface ClientInterface
{
    /**
     * Any implementing HTTP providers should send a request to the provided endpoint with the parameters.
     * They should return, in string form, the response body and throw an exception on error.
     *
     * @param UriInterface $endpoint
     * @param mixed        $requestBody
     * @param array        $extraHeaders
     * @param string       $method
     *
     * @return string
     *
     * @throws TokenResponseException
     */
    public function retrieveResponse(
        UriInterface $endpoint,
        $requestBody,
        array $extraHeaders = array(),
        $method = 'POST'
    );
}
