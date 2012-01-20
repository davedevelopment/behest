<?php
/**
 * @package
 * @subpackage
 */

/**
 * TITLE
 *
 * DESCRIPTION
 *
 * @author      Dave Marshall <dave.marshall@atstsolutions.co.uk>
 */

var_dump($_SERVER['PHP_AUTH_USER']);
var_dump($_SERVER['PHP_AUTH_PW']);

if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] !== 'dave' || $_SERVER['PHP_AUTH_PW'] !== 'pass123') {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Text to send if user hits Cancel button';
    exit;
} else {
    echo "<p>Hello {$_SERVER['PHP_AUTH_USER']}.</p>";
    echo "<p>You entered {$_SERVER['PHP_AUTH_PW']} as your password.</p>";
}


