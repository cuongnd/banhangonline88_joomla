<?php
namespace GuzzleHttp\Message;
/**
 *
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage guzzlehttp
 * @subpackage guzzle
 * @subpackage Message
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
/**
 * Applies headers to a request.
 *
 * This interface can be used with Guzzle streams to apply body specific
 * headers to a request during the PREPARE_REQUEST priority of the before event
 *
 * NOTE: a body that implements this interface will prevent a default
 * content-type from being added to a request during the before event. If you
 * want a default content-type to be added, then it will need to be done
 * manually (e.g., using {@see GuzzleHttp\Mimetypes}).
 */
interface AppliesHeadersInterface
{
    /**
     * Apply headers to a request appropriate for the current state of the
     * object.
     *
     * @param RequestInterface $request Request
     */
    public function applyRequestHeaders(RequestInterface $request);
}
