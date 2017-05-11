<?php
require_once '../php-api/enigma.api.php';
$Enigma = new Enigma('../php-api/keys/01/rsa_1024_pub.pem', '../php-api/keys/01/rsa_1024_priv.pem');
$Enigma->go();


//header('Content-type: text/plain');
var_dump($_POST);
