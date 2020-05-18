<?php


namespace Epys\Wis\Network\Whatsapp;


class Stiker
{
    /**
     * Método para normalizar y pasar a json
     * @version        20.05.185.391
     */
    public static function Normalize($url, $caption)
    {

        return ["type" => "stiker", "url" => $url, "caption" => $caption];

    }
}
