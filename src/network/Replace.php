<?php


namespace Epys\Wis\Network;


class Replace
{
    /**
     * Método para remplazar textos entre corchetes
     * @version        20.05.185.391
     */
    public
    static function strtr($msje)
    {
        \Epys\Wis\Console::log("Epys\Wis\Network\Replace::strtr().");

        $remplazar = [];

        // remplazo variables de actividad temporal
        if (\Epys\Wis\Client::$activ)
            foreach (\Epys\Wis\Client::$activ as $Col => $Val)
                $remplazar["{" . $Col . "}"] = $Val;

        // remplazo variables de contacto
        if (\Epys\Wis\Client::$contact)
            foreach (\Epys\Wis\Client::$contact as $Col => $Val)
                $remplazar["{" . $Col . "}"] = $Val;

        // remplazo variables de troncal
        if (\Epys\Wis\Client::$trunk)
            foreach (\Epys\Wis\Client::$trunk as $Col => $Val)
                $remplazar["{" . $Col . "}"] = $Val;

        // remplazo variables de ivr
        if (\Epys\Wis\Client::$ivr)
            foreach (\Epys\Wis\Client::$ivr as $Col => $Val)
                $remplazar["{" . $Col . "}"] = $Val;

        // remplazo variables de preguntas
        if (\Epys\Wis\Client::$ask)
            foreach (\Epys\Wis\Client::$ask as $Col => $Val)
                $remplazar["{" . $Col . "}"] = $Val;

        return strtr($msje, $remplazar);

    }
}
