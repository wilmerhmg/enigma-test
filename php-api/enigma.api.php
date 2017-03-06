<?php
require_once 'third-party/sqAES.php';

class enigma{
    private $private_key_file;
    private $public_key_file;

    const SESSION_KEY = 'jEnigmaKey';
    const POST_KEY = 'Enigma';

    public function __construct($public_key_file, $private_key_file){
        $this->public_key_file = $public_key_file;
        $this->private_key_file = $private_key_file;

        if (!is_readable($this->private_key_file)) {
            throw new Exception('No se puede leer la clave privada');
        }
        if (!is_readable($this->public_key_file)) {
            throw new Exception('No se puede leer la clave publica');
        }

        $this->session_start();
    }

    public function getPublicKey(){
        Header('Content-type: application/json');
        echo json_encode(array('publickey' => file_get_contents($this->public_key_file)));
        exit();
    }

    public function handshake(){
        openssl_private_decrypt(base64_decode($_POST['key']), $key, file_get_contents($this->private_key_file));
        $_SESSION[self::SESSION_KEY] = $key;
        Header('Content-type: application/json');
        echo json_encode(array('challenge' =>  sqAES::crypt($key, $key)));
        exit();
    }

    public function decrypttest(){
        // Establecer zona horaria por si acaso
        date_default_timezone_set('UTC');
        // Obtener algunos datos de prueba para cifrar, se trata de una ISO 8601 timestamp
        $toEncrypt = date('c');

        // Obtener la clave de la sesión
        $key = $_SESSION[self::SESSION_KEY];

        $encrypted = sqAES::crypt($key, $toEncrypt);

        Header('Content-type: application/json');
        echo json_encode(
            array(
            'encrypted' => $encrypted,
            'unencrypted' => $toEncrypt,
        )
        );
        exit();
    }

    public static function decrypt(){
        self::session_start();
        parse_str(sqAES::decrypt($_SESSION[self::SESSION_KEY], $_POST[self::POST_KEY]), $_POST);
        //No se puede desmontar la clave aquí, se romperia el bidireccional.
        //unset($_SESSION[self::SESSION_KEY]);
        unset($_REQUEST[self::POST_KEY]);
        $_REQUEST = array_merge($_POST, $_REQUEST);
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

        if (isset($_POST[self::POST_KEY])) {
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
