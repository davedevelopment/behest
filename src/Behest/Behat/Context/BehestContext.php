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
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $httpAuthType;

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
     * Set client
     * 
     * @param Client $client
     * @return BehestContext
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Get Client
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
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
        $this->addHeaders($this->request);
        $this->addAuth($this->request);
        try {
            $this->response = $this->request->send();
        } catch (BadResponseException $he) {
            $this->response = $he->getResponse();
            $this->exception = $he;
        }
    }

    /**
     * Add any authentication to the request, pulled out to here so it can be
     * extended etc
     *
     * @param Request $request
     */
    protected function addAuth(Request $request)
    {
        if ($this->username !== null) {
            switch ($this->httpAuthType) {

                case CURLAUTH_BASIC:
                case CURLAUTH_DIGEST:
                    $request->setAuth($this->username, $this->password, $this->httpAuthType);
                    break;
            }
        }
    }

    /**
     * Add any headers, pulled out to here so it can extended etc
     *
     * @param Request $request
     */
    protected function addHeaders(Request $request)
    {
        $request->addHeaders($this->headers);
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
        $this->request = null;
        $this->response = null;
        $this->username = null;
        $this->password = null;
    }
}



