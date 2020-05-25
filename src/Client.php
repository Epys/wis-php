<?php

namespace Epys\Wis;

class Client
{

    const VERSION = "0.57.78";

    const BASE_API = "https://api.wis.cl";


    /**
     * Input de PHP
     */
    public static $args;


    /**
     * Conexión a base de datos
     */
    public static $database;

    /**
     * Network provider
     */
    public static $network;

    /**
     * Contact
     */
    public static $contact;

    /**
     * Provider
     */
    public static $provider;

    /**
     * Trunk
     */
    public static $trunk;

    /**
     * Activ
     */
    public static $activ;

    /**
     * Conversation
     */
    public static $conversation;

    /**
     * IVR
     */
    public static $ivr;

    /**
     * Ask
     */
    public static $ask;

    /**
     * API Token
     */
    public static $token;


    /**
     * @var array
     */
    protected $options = [];

    /**
     * Create a new API client using the provided token.
     */
    public function __construct($token = false, $options = [])
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::__construct().");

        $this->options = $options;

        // Path de logs
        if (isset($options["logs"]))
            \Epys\Wis\Console::setPath($options["logs"]);

        // Base de datos
        if (isset($options["database"]))
            self::setDatabase($options["database"]);

        // Base de datos
        if (isset($options["network"]))
            self::setNetwork($options["network"]);

        // Base de datos
        if (isset($options["google"])) {
            if ($options["google"]["messaging"])
                \Epys\Wis\Google\Messaging::setToken($options["google"]["messaging"]);
        }

        // Defino Token
        if ($token)
            self::setToken($token);

    }

    function __destruct()
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::__destruct().");
        \Epys\Wis\Console::destruct();
    }


    /**
     * Método para asignar token
     * @param $token Claves de acceso
     * @version        20.05.185.391
     */
    public static function setToken($token)
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::setToken(" . $token . ").");
        self::$token = $token;

    }

    /**
     * Método para asignar base de datos
     * @version        20.05.185.391
     */
    public static function setDatabase($db)
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::setDatabase().");
        self::$database = $db;
    }

    /**
     * Método para asignar IVR
     * @version        20.05.185.391
     */
    public static function setIvr($ivr)
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::setIvr().");
        self::$ivr = $ivr;
    }

    /**
     * Método para asignar pregunta
     * @version        20.05.185.391
     */
    public static function setAsk($ask)
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::setAsk().");
        self::$ask = $ask;
    }

    /**
     * Método para asignar transaccion al input
     * @version        20.05.185.391
     */
    public static function setArgstran($tran)
    {
        if (self::$args->transac < 1) {
            \Epys\Wis\Console::log("Epys\Wis\Client::setArgstran(" . $tran . ")");
            self::$args->transac = $tran;
        }
    }

    /**
     * Funcion para normalizar las variables de entrada
     * @version        20.05.185.391
     */
    public static function Normalize()
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::Normalize().");

        self::$args = Config\Normalize::Input();

    }

    /**
     * Funcion para definir network
     * @version        20.05.185.391
     */
    public static function setNetwork($net, $config = ["provider", "contact"])
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::setNetwork(" . $net . ").");

        switch ($net) {
            case "whatsapp":
                self::$network = new \Epys\Wis\Network\Whatsapp(
                    $config["provider"],
                    $config["contact"]
                );
                break;
        }
    }

    /**
     * Funcion para definir provider
     * @version        20.05.185.391
     */
    public static function setProvider($provider)
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::setProvider(" . $provider . ").");
        self::$provider = $provider;
    }

    /**
     * Funcion para retornar type de entrada
     * @version        20.05.185.391
     */
    public static function isType()
    {

        if (self::$args->message)
            return "message";

        if (self::$args->delivery)
            return "delivery";

        \Epys\Wis\Console::error("El objeto Payload no es valido. Ejecute la función ´isType()´ para capturar datos.", \Epys\Wis\Console::ERROR_INPUT_TYPE, __CLASS__, __LINE__);

    }

    /**
     * Funcion para retornar type de entrada
     * @version        20.05.185.391
     */
    public static function isLoad($arr = [])
    {
        foreach ($arr as $variable) {
            if (!self::${$variable})
                \Epys\Wis\Console::error("El objeto `self::$" . $variable . "` no esta definido.", \Epys\Wis\Console::ERROR_INPUT, __CLASS__, __LINE__);
        }
    }

    /**
     * Funcion para normalizar las variables de entrada
     * @version        20.05.185.391
     */
    public static function Contact()
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::Contact().");
        return self::$contact = \Epys\Wis\Config\Contact::Get();
    }


    /**
     * Funcion para normalizar las variables de entrada
     * @version        20.05.185.391
     */
    public static function Trunk()
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::Trunk().");
        self::$trunk = \Epys\Wis\Config\Trunk::Get();

        // Valido que la troncal tenga una acción especial
        /**********************************************************************************
         * ADVERTENCIA - ESTE COMANDO PUEDE PRODUCIÓN UN BLUCE DE MENSAJES
         **********************************************************************************/
        if (self::$trunk->BLOB_TRONCAL && self::isType() === "message") {
            $Blob = new \Epys\Wis\Util\Blob();
            eval('$Blob->run = function () { ' . self::$trunk->BLOB_TRONCAL . '};');
            $Blob->run();
        }


    }


    /**
     * Funcion para verificar si el contacto y la tecno tienen activs pendientes
     * @version        20.05.185.391
     */
    public static function Activ()
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::Activ().");
        return self::$activ = \Epys\Wis\Flow\Activtemp::getContactTecno();
    }


    /**
     * Funcion para verificar si el contacto y la tecno tienen conversaciones
     * @version        20.05.185.391
     */
    public static function Conversation()
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::Conversation().");
        return self::$conversation = \Epys\Wis\Config\Conversation::getContactTrunk();
    }


    /**
     * Funcion para inizializar el bot
     * @version        20.05.185.391
     */
    public static function Bot()
    {

        \Epys\Wis\Console::log("Epys\Wis\Client::Bot().");

        // Inizializo Bot
        new \Epys\Wis\Bot\Init();

    }

}
