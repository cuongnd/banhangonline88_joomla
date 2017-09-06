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
 * Base event interface used when dispatching events to listeners using an
 * event emitter.
 */
interface EventInterface
{
    /**
     * Returns whether or not stopPropagation was called on the event.
     *
     * @return bool
     * @see Event::stopPropagation
     */
    public function isPropagationStopped();

    /**
     * Stops the propagation of the event, preventing subsequent listeners
     * registered to the same event from being invoked.
     */
    public function stopPropagation();
}
