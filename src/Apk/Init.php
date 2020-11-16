<?php


namespace Epys\Wis\Apk;


class Init
{

    /**
     * URL
     */
    const FLOW = \Epys\Wis\Client::BASE_API . "/flow";

    /**
     * Create a new Apk.
     */
    public function __construct()
    {
        \Epys\Wis\Console::log("Epys\Wis\Bot\Init().");
    }

    /**
     * Método para solicitar token
     * @version        20.11.302.503
     */
    public
    function token($number, $iden, $name, $provider)
    {
        \Epys\Wis\Console::log("Epys\Wis\Apk\Init::token.");

        $json = [
            "number" => $number,
            "iden" => $iden,
            "name" => $name,
            "provider" => $provider
        ];

        // Retorno resultado
        return \Epys\Wis\Http\Service::POST(self::FLOW . "/token", $json);
    }


}
