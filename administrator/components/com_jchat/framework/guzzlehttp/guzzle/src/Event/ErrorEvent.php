<?php
namespace GuzzleHttp\Event;
/**
 *
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage guzzlehttp
 * @subpackage guzzle
 * @subpackage Event
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
use GuzzleHttp\Exception\RequestException;

/**
 * Event emitted when an error occurs while sending a request.
 *
 * This event MAY be emitted multiple times. You MAY intercept the exception
 * and inject a response into the event to rescue the request using the
 * intercept() method of the event.
 *
 * This event allows the request to be retried using the "retry" method of the
 * event.
 */
class ErrorEvent extends AbstractRetryableEvent
{
    /**
     * Get the exception that was encountered
     *
     * @return RequestException
     */
    public function getException()
    {
        return $this->transaction->exception;
    }
}
