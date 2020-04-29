<?php


namespace Epys\Wis\Network\Whatsapp;


class Image
{
    /**
     * Método para normalizar y pasar a json
     * @version 2020-04-20
     */
    public static function Normalize($url, $caption)
    {

        return ["type" => "image", "url" => $url, "caption" => $caption];

    }
}
