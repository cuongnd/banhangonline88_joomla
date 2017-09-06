<?php
namespace GuzzleHttp\Subscriber;
/**
 *
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage guzzlehttp
 * @subpackage guzzle
 * @subpackage Subscriber
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Throws exceptions when a 4xx or 5xx response is received
 */
class HttpError implements SubscriberInterface
{
    public function getEvents()
    {
        return ['complete' => ['onComplete', RequestEvents::VERIFY_RESPONSE]];
    }

    /**
     * Throw a RequestException on an HTTP protocol error
     *
     * @param CompleteEvent $event Emitted event
     * @throws RequestException
     */
    public function onComplete(CompleteEvent $event)
    {
        $code = (string) $event->getResponse()->getStatusCode();
        // Throw an exception for an unsuccessful response
        if ($code[0] >= 4) {
            throw RequestException::create(
                $event->getRequest(),
                $event->getResponse()
            );
        }
    }
}
