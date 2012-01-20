<?php
namespace Behest\Behat\Context;

use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\BadResponseException;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

use PHPUnit_Framework_ExpectationFailedException as AssertException;

/**
 * Behest context. A simple context for interacting with RESTful apis
 *
 * @author      Dave Marshall <dave.marshall@atstsolutions.co.uk>
 */
class BehestContext extends BehatContext
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Exception
     */
    protected $exception;

    /**
     * @var array
     *
     * Headers for the next request
     */
    protected $headers = array();

    /**
     * Constructor
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @When /^I send a GET request to "([^"]*)"$/
     */
    public function iSendAGetRequestTo($path)
    {
        $this->send('GET', $path);
    }

    /**
     * @Then /^the response status code should be "([^"]*)"$/
     */
    public function theResponseStatusCodeShouldBe($code)
    {
        $message = sprintf('Status code was "%d", but "%d" expected', $this->response->getStatusCode(), $code);
        assertEquals($code, $this->response->getStatusCode(), $message); 
    }

    /**
     * @Given /^I accept "([^"]*)"$/
     */
    public function iAccept($contentType)
    {
        $this->headers['Accept'] = $contentType;
    }

    /**
     * @Given /^I print the last response body$/
     */
    public function iPrintTheLastResponseBody()
    {
        $this->printDebug($this->response->getBody(true));
    }

    /**
     * @Given /^I print the last response headers$/
     */
    public function iPrintTheLastResponseHeaders()
    {
        $headers = '';
        foreach($this->response->getHeaders() as $key => $value) {
            $headers.= $key . ': ' . $value . PHP_EOL;
        };

        $this->printDebug($headers);
    }

    /**
     * Send the request
     *
     * Sends a request, along with any headers/auth specified previously
     *
     * @param string method
     * @param string $body
     * @return void
     */
    protected function send($method, $path, $body = '')
    {
        $this->request = $this->client->{$method}($path, $body);
        $this->request->addHeaders($this->headers);
        try {
            $this->response = $this->request->send();
        } catch (BadResponseException $he) {
            $this->response = $he->getResponse();
            $this->exception = $he;
        }
    }

    /**
     * @BeforeScenario
     */
    public function init()
    {
        /**
         * Reset things
         */
        $this->headers = array();
    }
}



