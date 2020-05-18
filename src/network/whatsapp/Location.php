<?php


namespace Epys\Wis\Network\Whatsapp;


class Location
{
    /**
     * Método para normalizar y pasar a json
     * @version        20.05.185.391
     */
    public static function Normalize($text)
    {
        // Remplazo palabras entre corchetes
        $text = \Epys\Wis\Network\Replace::strtr($text);

        // Retorno texto
        return ["type" => "text", "text" => $text];

    }
}
