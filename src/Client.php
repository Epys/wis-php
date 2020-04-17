<?php

namespace Epys\Wis;
use Epys\Wis\Console;



class Client
{

    const VERSION = '0.1.55';

    const BASE_API = 'https://api.wis.cl/whatsapp';

    /**
     * API Token
     * @var CredentialsInterface
     */
    protected static $token;


    /**
     * @var array
     */
    protected $options = [];

    /**
     * Create a new API client using the provided token.
     */
    public function __construct($token, $options = [])
    {

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
        self::$token = $token;
        Epys\Wis\Console::log(0, 'Asigno Token');
    }
}
