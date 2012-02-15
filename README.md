Behest - A simple REST client Behat Context
===========================================

Behest is a BehatContext that makes it easy for you to write features that call
RESTful web services

*WARNING*

I've just found out that Behat itself as something similar in the works, so I'll
probably stop working on this! Checkout
https://github.com/Behat/CommonContexts/blob/master/Behat/CommonContexts/WebApiContext.php


Installation
------------

Composer/packagist support coming soon

Usage
-----

In your `FeatureContext` class

``` php
<?php 

    public function __construct(array $parameters) 
    {
        $client = new Guzzle\Http\Client('http://api.twitter.com/1');
        $this->useContext('behest', new Behest\Behat\Context\BehestContext($client));
    }

```

Then in your feature files

``` cucumber
    When I send a GET request to "/statuses/public_timeline.json"
    Then the response status code should be "200"

```

See the features directory for more usage examples, or run `bin/behat -dl`


Coming Soon
-----------

PUT, POST, DELETE, etc

Possibly include some json steps ala collectiveidea/json_spec, although it might
be better to include that in a different library. For my current project, I've
got the following working

``` cucumber

    Then the JSON response at "results/0/_embedded/profile/first_name" should be "Dave"
    And the JSON response should have "2" "results"

```

I'd also like a way of following urls to support discoverable hypermedia etc,
something like

``` cucumber

    And I remember the JSON response at "results/0/_embedded/profile/_links/self/href" as "url"
    When I send a GET request to "%{url}"

```
