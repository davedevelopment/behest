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

echo "Content-type: " . (isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '') . "\n";
echo "Accept: " . $_SERVER['HTTP_ACCEPT'] . "\n";
echo "Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "Input: " . file_get_contents('php://input') . "\n";


