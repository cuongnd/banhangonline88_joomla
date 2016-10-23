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
 * Event object emitted after a request has been completed.
 *
 * This event MAY be emitted multiple times for a single request. You MAY
 * change the Response associated with the request using the intercept()
 * method of the event.
 *
 * This event allows the request to be retried if necessary using the retry()
 * method of the event.
 */
class CompleteEvent extends AbstractRetryableEvent {}
