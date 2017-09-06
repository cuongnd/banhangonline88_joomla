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
 * Trait that implements the methods of HasEmitterInterface
 */
trait HasEmitterTrait
{
    /** @var EmitterInterface */
    private $emitter;

    public function getEmitter()
    {
        if (!$this->emitter) {
            $this->emitter = new Emitter();
        }

        return $this->emitter;
    }
}
