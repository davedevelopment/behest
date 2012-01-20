Feature: Authentication
    In order to authenticate my request to a web service API
    As a consumer
    I need to be able to provide credentials

    Scenario: Basic HTTP Auth
        When I send a GET request to "/basic_auth.php"
        Then the api response status code should be "401"
        Given I am api user "dave" with the password "pass123"
        And I send "basic" authentication
        When I send a GET request to "/basic_auth.php"
        Then the api response status code should be "200"

     Scenario: Digest HTTP Auth
        When I send a GET request to "/digest_auth.php"
        Then the api response status code should be "401"
        Given I am api user "dave" with the password "pass123"
        And I send "digest" authentication
        When I send a GET request to "/digest_auth.php"
        Then the api response status code should be "200"
               
