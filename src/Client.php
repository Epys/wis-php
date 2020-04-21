<?php

namespace Epys\Wis;


class Client
{

    const VERSION = '0.11.05';

    const BASE_API = 'https://api.wis.cl';

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
    protected $options = [
        'logs' => '' // Path donde se guardan los logs
    ];

    /**
     * Create a new API client using the provided token.
     */
    public function __construct($token = false, $options = [])
    {

        $this->options = $options;

        // Path de logs
        if (isset($options['logs']))
            \Epys\Wis\Console::setPath($options['logs']);

        // Base de datos
        if (isset($options['database']))
            self::setDatabase($options['database']);

        // Defino Token
        if ($token)
            self::setToken($token);

    }

    function __destruct()
    {
        \Epys\Wis\Console::destruct();
    }


    /**
     * Método para asignar token
     * @param $token Claves de acceso
     * @version 2020-04-17
     */
    public static function setToken($token)
    {
        \Epys\Wis\Console::log('Defino token de conexión [' . $token . '].');
        self::$token = $token;

    }

    /**
     * Método para asignar base de datos
     * @version 2020-04-17
     */
    public static function setDatabase($db)
    {
        \Epys\Wis\Console::log('Agrego conexion a base de datos.');
        self::$database = $db;
    }

    /**
     * Método para asignar IVR
     * @version 2020-04-20
     */
    public static function setIvr($ivr)
    {
        \Epys\Wis\Console::log('Agrego IVR.');
        self::$ivr = $ivr;
    }

    /**
     * Método para asignar pregunta
     * @version 2020-04-20
     */
    public static function setAsk($ask)
    {
        \Epys\Wis\Console::log('Agrego pregunta.');
        self::$ask = $ask;
    }

    /**
     * Funcion para normalizar las variables de entrada
     * @version 2020-04-17
     */
    public static function Normalize()
    {
        self::$args = Config\Normalize::Input();

        // Seteo Network
        switch (self::$args->network) {
            case 'whatsapp':
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
            \Epys\Wis\Console::error('El objeto TYPE no es valido. Ejecute la función ´self::$args´ para capturar datos.', \Epys\Wis\Console::ERROR_INPUT_TYPE, __CLASS__, __LINE__);

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
                \Epys\Wis\Console::error('El objeto `self::$' . $variable . '` no esta definido.', \Epys\Wis\Console::ERROR_INPUT, __CLASS__, __LINE__);
        }
    }

    /**
     * Funcion para normalizar las variables de entrada
     * @version 2020-04-17
     */
    public static function Contact()
    {
        return self::$contact = \Epys\Wis\Config\Contact::Get();
    }


    /**
     * Funcion para normalizar las variables de entrada
     * @version 2020-04-18
     */
    public static function Trunk()
    {
        return self::$trunk = \Epys\Wis\Config\Trunk::Get();
    }


    /**
     * Funcion para verificar si el contacto y la tecno tienen activs pendientes
     * @version 2020-04-19
     */
    public static function Activ()
    {
        return self::$activ = \Epys\Wis\Flow\Activtemp::getContactTecno();
    }


    /**
     * Funcion para verificar si el contacto y la tecno tienen conversaciones
     * @version 2020-04-19
     */
    public static function Conversation()
    {
        return self::$conversation = \Epys\Wis\Bot\Conversation::getContactTrunk();
    }


    /**
     * Funcion para inizializar el bot
     * @version 2020-04-19
     */
    public static function Bot()
    {

        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Client::Bot().');

        // Inizializo Bot
        new \Epys\Wis\Bot\Init();

    }

}
