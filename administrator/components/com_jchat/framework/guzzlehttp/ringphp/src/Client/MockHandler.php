<?php
namespace GuzzleHttp\Ring\Client;
/**
 *
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage guzzlehttp
 * @subpackage ringphp
 * @subpackage Client
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
use GuzzleHttp\Ring\Core;
use GuzzleHttp\Ring\Future\CompletedFutureArray;
use GuzzleHttp\Ring\Future\FutureArrayInterface;

/**
 * Ring handler that returns a canned response or evaluated function result.
 */
class MockHandler
{
    /** @var callable|array|FutureArrayInterface */
    private $result;

    /**
     * Provide an array or future to always return the same value. Provide a
     * callable that accepts a request object and returns an array or future
     * to dynamically create a response.
     *
     * @param array|FutureArrayInterface|callable $result Mock return value.
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    public function __invoke(array $request)
    {
        Core::doSleep($request);
        $response = is_callable($this->result)
            ? call_user_func($this->result, $request)
            : $this->result;

        if (is_array($response)) {
            $response = new CompletedFutureArray($response + [
                'status'        => null,
                'body'          => null,
                'headers'       => [],
                'reason'        => null,
                'effective_url' => null,
            ]);
        } elseif (!$response instanceof FutureArrayInterface) {
            throw new \InvalidArgumentException(
                'Response must be an array or FutureArrayInterface. Found '
                . Core::describeType($request)
            );
        }

        return $response;
    }
}
