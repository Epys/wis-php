<?php

namespace Epys\Wis;



class Client
{

    const VERSION = '0.05.52';

    const BASE_API = 'https://api.wis.cl';

    /**
     * Input de PHP
     */
    protected static $arg;

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


        // Defino Token
        if ($token)
            self::setToken($token);


    }


    /**
     * Método para asignar token
     * @author Adonías Vasquez (adonias.vasquez[at]epys.cl)
     * @version 2020-04-17
     */
    public static function setToken($token)
    {
        \Epys\Wis\Console::log('Defino token de conexión [' . $token . ']');
        self::$token = $token;

    }

    /**
     * Funcion para normalizar las variables de entrada
     * @author Adonías Vasquez (adonias.vasquez[at]epys.cl)
     * @version 2020-04-17
     */
    public static function Normalize()
    {
        //self::$arg = Normalize::Input();
        //new \Epys\Wis\Config\Normalize();

    }
}
