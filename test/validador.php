<?php
require_once '../php-api/enigma.api.php';
$Enigma = new Enigma();
$Enigma->go();

$option = isset($_POST['option']) ? $_POST['option'] : FALSE;
$nombres = isset($_POST['nombres']) ? $_POST['nombres'] : FALSE;
$email = isset($_POST['email']) ? $_POST['email'] : FALSE;
$card_number = isset($_POST['card_number']) ? $_POST['card_number'] : FALSE;
$card_date = isset($_POST['card_date']) ? $_POST['card_date'] : FALSE;
$card_cvs = isset($_POST['card_cvs']) ? $_POST['card_cvs'] : FALSE;

$informacion = array('info'=>'Pago suscripcion realizada','codigo'=>'7CV334VB5CC2DDD34','usuario'=>'Carlos','clave'=>'tomates2017');

echo $Enigma->encrypt(json_encode($informacion));
