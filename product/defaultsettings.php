<?php
header("Content-Type:application/json");
$productName = "defaultsettings";
$memory = new Memcached();
$memory->addServer("127.0.0.1", 11211);

include __DIR__ . '/utils.php';

if (isset($_GET['versions']) && $_GET['versions'] == "") {
    versions($productName , $memory);
} else if (isset($_GET['latest']) && $_GET['latest'] != "") {
    latest($_GET['latest'], $productName , $memory);
} else if (isset($_GET['endpoint']) && $_GET['endpoint'] != "") {
    endpoint($_GET['endpoint'], $productName , $memory);
} else if (isset($_GET['endpoint2']) && $_GET['endpoint2'] != "") {
    endpoint2($_GET['endpoint2'], $productName , $memory);
} else {
    http_response_code(400);
}