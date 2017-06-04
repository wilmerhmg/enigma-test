<?php
require_once '../php-api/enigma.api.php';
$Enigma = new Enigma();
$Enigma->go();


header('Content-type: text/plain');
print_r($_POST);
