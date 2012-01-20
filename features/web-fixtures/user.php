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

$id = isset($_GET['id']) ?  $_GET['id'] : null;

$users = array(
    array('username' => 'davedevelopment'),
    array('username' => 'evie'),
    array('username' => 'rebecca'),
    array('username' => 'murphy'),
    array('username' => 'narla'),
);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if ($id) { 
        if (!isset($users[$id])) {
            header('HTTP/1.1 404 Not Found');
            exit;
        }

        $data = $users[$id];
    } else {
        $data = $users;
    }

    if ($_SERVER['HTTP_ACCEPT'] == "*/*") {
        header("Content-type: text/plain");
        echo serialize($data);
        exit;
    } else if ($_SERVER['HTTP_ACCEPT'] == "application/json") {
        header("Content-type: applciation/json");
        echo json_encode($data);
        exit;
    } else {
        header("HTTP/1.1 406 Not Acceptable");
        exit;
    }

}


