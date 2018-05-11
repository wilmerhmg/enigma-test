<?php
require_once '../php-api/enigma.api.php';
$Enigma = new Enigma();
$Enigma->go();

$option    = isset($_POST['servicios']) ? $_POST['servicios'] : FALSE;
$nombres   = isset($_POST['nombres']) ? $_POST['nombres'] : FALSE;
$email     = isset($_POST['email']) ? $_POST['email'] : FALSE;
$codigo    = isset($_POST['codigo']) ? $_POST['codigo'] : FALSE;
$card_date = isset($_POST['card_date']) ? $_POST['card_date'] : FALSE;
$card_cvs  = isset($_POST['card_cvs']) ? $_POST['card_cvs'] : FALSE;

$informacion = array(
	'info'       => 'Pago suscripcion realizada',
	'referencia' => '7CV334VB5CC2DDD34',
	'codigo'     => $codigo,
	'servicios'  => $option
);


//Usa el metodo encrypt de la clase Enigma para retornar valores cifrados desde el servidor hacia el cliente
echo $Enigma->encrypt(json_encode($informacion));
