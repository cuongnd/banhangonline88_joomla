<?php
namespace GuzzleHttp\Exception;
/**
 *
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage guzzlehttp
 * @subpackage guzzle
 * @subpackage Exception
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
use GuzzleHttp\Message\ResponseInterface;

/**
 * Exception when a client is unable to parse the response body as XML
 */
class XmlParseException extends ParseException
{
    /** @var \LibXMLError */
    protected $error;

    public function __construct(
        $message = '',
        ResponseInterface $response = null,
        \Exception $previous = null,
        \LibXMLError $error = null
    ) {
        parent::__construct($message, $response, $previous);
        $this->error = $error;
    }

    /**
     * Get the associated error
     *
     * @return \LibXMLError|null
     */
    public function getError()
    {
        return $this->error;
    }
}
