<?php

require_once("config/config.php");

$api = new Api();
if (isset($_GET['a'])) {
    $action = $_GET['a'];
} else {
    json_encode(['status' => "Error", "message" => "Please type get param"]);
    exit();
}
switch ($action) {
    case 'request':
        $api->request();
        break;
    case 'send':
        $api->send();
        break;
    case 'open_door':
        $api->openDoor();
        break;
    case 'close_door':
        $api->closeDoor();
        break;
    case 'alarm':
        $api->alarm();
        break;
}

