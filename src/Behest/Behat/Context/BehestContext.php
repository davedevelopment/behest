<?php
namespace Behest\Behat\Context;

use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Exception\BadResponseException;

use Behat\Gherkin\Node\PyStringNode;
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
     * @When /^I send a (POST|GET|PUT|DELETE) request to "([^"]*)"(?:| with the following:)$/
     */
    public function iSendARequestToWithTheFollowing($method, $url, PyStringNode $data = null)
    {
        $this->send($method, $url, (string) $data);
    }

    /**
     * @Then /^the api response status code should be "([^"]*)"$/
     */
    public function theApiResponseStatusCodeShouldBe($code)
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
     * @Given /^I send "([^"]*)"$/
     */
    public function iSend($contentType)
    {
        $this->headers['Content-Type'] = $contentType;
    }

    /**
     * @Given /^I print the last (request|response) body$/
     */
    public function iPrintTheLastResponseBody($type)
    {
        $this->printDebug($this->{$type}->getBody(true));
    }

    /**
     * @Given /^I print the last (response|request) headers$/
     */
    public function iPrintTheLastResponseHeaders($type)
    {
        $headers = '';
        foreach($this->{$type}->getHeaders() as $key => $value) {
            foreach ((array) $value as $v) {
                $headers.= $key . ': ' . $v . PHP_EOL;
            }
        };

        $this->printDebug($headers);
    }

    /**
     * @Given /^I am api user "([^"]*)" with the password "([^"]*)"$/
     */
    public function iAmApiUserWithThePassword($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @Given /^I send "([^"]*)" authentication$/
     */
    public function iSendAuthentication($type)
    {
        $this->httpAuthType = $type;
    }

    /**
     * @Given /^the last response body should contain "([^"]*)"$/
     */
    public function theResponseBodyShouldContain($test)
    {
        $data = $this->response->getBody(true);
        assertTrue(false !== strpos($data, $test));
    }

    /**
     * @Given /^the last response "([^"]*)" header should contain "([^"]*)"$/
     */
    public function theLastResponseHeaderShouldContain($key, $val)
    {
        $data = $this->response->getHeader($key);
        assertTrue(false !== strpos($data, $val));
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
        $this->request = $this->client->{$method}($path, array(), $body);
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
                case 'basic':
                    $request->setAuth($this->username, $this->password, CURLAUTH_BASIC);
                    break;
                case 'digest':
                    $request->setAuth($this->username, $this->password, CURLAUTH_DIGEST);
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
    public function initBehestContext()
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



