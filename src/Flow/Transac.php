<?php


namespace Epys\Wis\Flow;


class Transac
{

    /**
     * Método para buscar mensajes de una transacción
     * @version 2020-04-19
     */
    public static function getMenssages($id)
    {
        \Epys\Wis\Console::log("Epys\Wis\Flow\Transac::getMenssages().");


    }

    /**
     * Método para buscar mensajes de una transacc
     * @version 2020-04-20
     */
    public
    function messages($provider = null, $contact = null, $transac = null)
    {
        \Epys\Wis\Console::log("Epys\Wis\Network\Whatsapp::messages().");

        $json = [
            "transac" => self::$_transac,
            "contact" => ["number" => self::$_contact],
            "content" => self::$_content,
            "provider" => ["number" => self::$_provider]
        ];

        // Retorno resultado
        $result = \Epys\Wis\Http\Service::POST(self::URL, $json);

        return $result;

    }
}
