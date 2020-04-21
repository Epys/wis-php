<?php


namespace Epys\Wis\Network\Whatsapp;


class Text
{
    /**
     * Método para normalizar y pasar a json
     * @version 2020-04-20
     */
    public static function Normalize($text)
    {
        // Remplazo palabras entre corchetes
        $text = \Epys\Wis\Network\Replace::strtr($text);

        // Retorno texto
        return ['type' => 'text', 'text' => $text];

    }
}
