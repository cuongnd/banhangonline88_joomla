<?php
namespace GuzzleHttp\Ring\Future;
/**
 *
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage guzzlehttp
 * @subpackage ringphp
 * @subpackage Future
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
/**
 * Implements common future functionality that is triggered when the result
 * property is accessed via a magic __get method.
 *
 * @property mixed $_value Actual data used by the future. Accessing this
 *     property will cause the future to block if needed.
 */
trait MagicFutureTrait
{
    use BaseFutureTrait;

    /**
     * This function handles retrieving the dereferenced result when requested.
     *
     * @param string $name Should always be "data" or an exception is thrown.
     *
     * @return mixed Returns the dereferenced data.
     * @throws \RuntimeException
     * @throws \GuzzleHttp\Ring\Exception\CancelledException
     */
    public function __get($name)
    {
        if ($name !== '_value') {
            throw new \RuntimeException("Class has no {$name} property");
        }

        return $this->_value = $this->wait();
    }
}
