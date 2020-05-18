<?php


namespace Epys\Wis\Network\Whatsapp;


class Image
{
    /**
     * Método para normalizar y pasar a json
     * @version        20.05.185.391
     */
    public static function Normalize($url, $caption)
    {

        return ["type" => "image", "url" => $url, "caption" => $caption];

    }
}
