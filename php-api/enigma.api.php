<?php
require_once 'third-party/sqAES.php';

class enigma{
    private $private_key_file;
    private $public_key_file;

    const SESSION_KEY = 'jEnigmaKey';
    const POST_KEY = 'Enigma';
    const COUNT_ENIGMA = 'Enigma_count';

    public function __construct($public_key_file, $private_key_file){
        $this->session_start();
        //Generar Llave Aleatoria
        if(!isset($_SESSION[self::COUNT_ENIGMA]) || !$_SESSION[self::COUNT_ENIGMA]){

        }
        $this->public_key_file = $public_key_file;
        $this->private_key_file = $private_key_file;

        if (!is_readable($this->private_key_file)) {
            throw new Exception('No se puede leer la clave privada');
        }
        if (!is_readable($this->public_key_file)) {
            throw new Exception('No se puede leer la clave publica');
        }

    }

    public function getPublicKey(){
        Header('Content-type: application/json');
        echo json_encode(array('publickey' => file_get_contents($this->public_key_file)));
        $_SESSION[self::COUNT_ENIGMA]=1;
        exit();
    }

    public function handshake(){
        openssl_private_decrypt(base64_decode($_POST['key']), $key, file_get_contents($this->private_key_file));
        $_SESSION[self::SESSION_KEY] = $key;
        Header('Content-type: application/json');
        echo json_encode(array('challenge' =>  sqAES::crypt($key, $key)));
        $_SESSION[self::COUNT_ENIGMA]=2;
        exit();
    }

    public function decrypttest(){
        // Establecer zona horaria por si acaso a una hora internacional
        date_default_timezone_set('UTC');
        // Obtener algunos datos de prueba para cifrar
        $toEncrypt = date('c');

        // Obtener la clave de la sesión
        $key = $_SESSION[self::SESSION_KEY];

        $encrypted = sqAES::crypt($key, $toEncrypt);

        Header('Content-type: application/json');
        echo json_encode(array(
            'encrypted' => $encrypted,
            'unencrypted' => $toEncrypt
        ));
        exit();
    }

    public static function decrypt(){
        self::session_start();
        parse_str(sqAES::decrypt($_SESSION[self::SESSION_KEY], $_REQUEST[self::POST_KEY]), $_REQUEST);
        //No se puede desmontar la clave aquí, se romperia el bidireccional.
        //unset($_SESSION[self::SESSION_KEY]);
        unset($_REQUEST[self::POST_KEY]);
        unset($_POST[self::POST_KEY]);
        unset($_GET[self::POST_KEY]);
        $_REQUEST = array_merge($_REQUEST, $_REQUEST);
        $_POST = array_merge($_POST,$_REQUEST);
        $_GET = array_merge($_GET,$_REQUEST);
        $_SESSION[self::COUNT_ENIGMA] = 0;
    }

    public function go(){

        if (isset($_GET['getPublicKey'])) {
            $this->getPublicKey();
        }

        if (isset($_GET['handshake'])) {
            $this->handshake();
        }

        if (isset($_GET['decrypttest'])) {
            $this->decrypttest();
        }

        if (isset($_POST[self::POST_KEY]) || isset($_GET[self::POST_KEY])) {
            $this->decrypt();
        }
    }

    public static function session_start(){
        switch (session_status()) {
            case PHP_SESSION_DISABLED :
                throw new Exception('Enigma requiere variables de session (Habilite las sessiones de PHP)');
                break;
            case PHP_SESSION_NONE :
                session_start();
                break;
        }
    }
}
