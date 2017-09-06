<?php
namespace React\Promise;
/**
 *
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage react
 * @subpackage promise
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class UnhandledRejectionException extends \RuntimeException
{
    private $reason;

    public static function resolve($reason)
    {
        if ($reason instanceof \Exception) {
            return $reason;
        }

        return new static($reason);
    }

    public function __construct($reason)
    {
        $this->reason = $reason;

        $message = sprintf('Unhandled Rejection: %s', json_encode($reason));

        parent::__construct($message, 0);
    }

    public function getReason()
    {
        return $this->reason;
    }
}
