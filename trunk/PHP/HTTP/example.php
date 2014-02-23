<?php
define('__HVE','HansVonEngine');
require_once('http.class.php');

$conn = new H_HTTP('example.com', 80);
$conn->debugOn();
$conn->GET('/');

echo $conn->RESPONSE();
?>