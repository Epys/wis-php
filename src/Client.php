<?php

namespace Epys\Wis;


class Client
{

    const VERSION = "0.19.49";

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
    protected static $token;


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
     * @version 2020-04-17
     */
    public static function setToken($token)
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::setToken(" . $token . ").");
        self::$token = $token;

    }

    /**
     * Método para asignar base de datos
     * @version 2020-04-17
     */
    public static function setDatabase($db)
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::setDatabase().");
        self::$database = $db;
    }

    /**
     * Método para asignar IVR
     * @version 2020-04-20
     */
    public static function setIvr($ivr)
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::setIvr().");
        self::$ivr = $ivr;
    }

    /**
     * Método para asignar pregunta
     * @version 2020-04-20
     */
    public static function setAsk($ask)
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::setAsk().");
        self::$ask = $ask;
    }

    /**
     * Método para asignar transaccion al input
     * @version 2020-04-20
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
     * @version 2020-04-17
     */
    public static function Normalize()
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::Normalize().");

        self::$args = Config\Normalize::Input();

        self::setNetwork(self::$args->network, [
            self::$args->provider->number,
            self::$args->contact->number
        ]);
    }

    /**
     * Funcion para definir network
     * @version 2020-04-17
     */
    public static function setNetwork($net, $config = ["provider", "contact"])
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::setNetwork(" . $net . ").");

        switch ($net) {
            case "whatsapp":
                self::$network = new \Epys\Wis\Network\Whatsapp(
                    self::$args->provider->number,
                    self::$args->contact->number
                );
                break;
        }
    }

    /**
     * Funcion para retornar type de entrada
     * @version 2020-04-17
     */
    public static function isType()
    {
        if (!self::$args->type)
            \Epys\Wis\Console::error("El objeto TYPE no es valido. Ejecute la función ´self::$args´ para capturar datos.", \Epys\Wis\Console::ERROR_INPUT_TYPE, __CLASS__, __LINE__);

        // Retorno datos
        return self::$args->type;
    }

    /**
     * Funcion para retornar type de entrada
     * @version 2020-04-17
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
     * @version 2020-04-17
     */
    public static function Contact()
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::Contact().");
        return self::$contact = \Epys\Wis\Config\Contact::Get();
    }


    /**
     * Funcion para normalizar las variables de entrada
     * @version 2020-04-18
     */
    public static function Trunk()
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::Trunk().");
        return self::$trunk = \Epys\Wis\Config\Trunk::Get();
    }


    /**
     * Funcion para verificar si el contacto y la tecno tienen activs pendientes
     * @version 2020-04-19
     */
    public static function Activ()
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::Activ().");
        return self::$activ = \Epys\Wis\Flow\Activtemp::getContactTecno();
    }


    /**
     * Funcion para verificar si el contacto y la tecno tienen conversaciones
     * @version 2020-04-19
     */
    public static function Conversation()
    {
        \Epys\Wis\Console::log("Epys\Wis\Client::Conversation().");
        return self::$conversation = \Epys\Wis\Config\Conversation::getContactTrunk();
    }


    /**
     * Funcion para inizializar el bot
     * @version 2020-04-19
     */
    public static function Bot()
    {

        \Epys\Wis\Console::log("Epys\Wis\Client::Bot().");

        // Inizializo Bot
        new \Epys\Wis\Bot\Init();

    }

}
