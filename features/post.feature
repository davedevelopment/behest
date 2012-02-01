Feature: Send post requests
    In order to add or update resources
    As an API consumer
    I need to send POST requests

Scenario: Send a post with content-type
    Given I send "application/json"
    When I send a POST request to "/request-info.php" with the following:
        """
        DAVEDAVEDAVE
        """
    Then the api response status code should be "200" 
    And the last response body should contain "Content-type: application/json"
    And the last response body should contain "Input: DAVEDAVEDAVE"
