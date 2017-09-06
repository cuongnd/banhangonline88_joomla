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
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;

/**
 * Adds, extracts, and persists cookies between HTTP requests
 */
class Cookie implements SubscriberInterface
{
    /** @var CookieJarInterface */
    private $cookieJar;

    /**
     * @param CookieJarInterface $cookieJar Cookie jar used to hold cookies
     */
    public function __construct(CookieJarInterface $cookieJar = null)
    {
        $this->cookieJar = $cookieJar ?: new CookieJar();
    }

    public function getEvents()
    {
        // Fire the cookie plugin complete event before redirecting
        return [
            'before'   => ['onBefore'],
            'complete' => ['onComplete', RequestEvents::REDIRECT_RESPONSE + 10]
        ];
    }

    /**
     * Get the cookie cookieJar
     *
     * @return CookieJarInterface
     */
    public function getCookieJar()
    {
        return $this->cookieJar;
    }

    public function onBefore(BeforeEvent $event)
    {
        $this->cookieJar->addCookieHeader($event->getRequest());
    }

    public function onComplete(CompleteEvent $event)
    {
        $this->cookieJar->extractCookies(
            $event->getRequest(),
            $event->getResponse()
        );
    }
}
