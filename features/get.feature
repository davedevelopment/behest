Feature: Send get requests
    In order to retrieve resources from the API
    As an API consumer
    I need to be able to send GET requests

Scenario: GET a resource
    When I send a GET request to "/user.php?id=1"
    Then the response status code should be "200"

Scenario: Try and GET a resource that is not available 
    When I send a GET request to "/user.php?id=200"
    Then the response status code should be "404"

Scenario: GET a application/json representation of a resource
    Given I accept "application/json"
    When I send a GET request to "/user.php?id=1"
    Then the response status code should be "200"

Scenario: Try and GET a unacceptable representation of a resource
    Given I accept "application/made-up-media-type" 
    When I send a GET request to "/user.php?=1"
    Then the response status code should be "406"