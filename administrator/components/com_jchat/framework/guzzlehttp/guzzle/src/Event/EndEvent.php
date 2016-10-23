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
/**
 * A terminal event that is emitted when a request transaction has ended.
 *
 * This event is emitted for both successful responses and responses that
 * encountered an exception. You need to check if an exception is present
 * in your listener to know the difference.
 *
 * You MAY intercept the response associated with the event if needed, but keep
 * in mind that the "complete" event will not be triggered as a result.
 */
class EndEvent extends AbstractTransferEvent
{
    /**
     * Get the exception that was encountered (if any).
     *
     * This method should be used to check if the request was sent successfully
     * or if it encountered errors.
     *
     * @return \Exception|null
     */
    public function getException()
    {
        return $this->transaction->exception;
    }
}
