<?php define('__HVE','HansVonEngine');
require_once('../HTTP/http.class.php');
require_once('file.upload.class.php');

$conn = new H_FILE_UPLOAD('cgi-lib.berkeley.edu', 80);
$sd = array('upfile'=>'example.php');
$conn->upload('/ex/fup.cgi', $sd);

echo $conn->RESPONSE();
?>