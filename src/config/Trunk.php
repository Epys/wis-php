<?php


namespace Epys\Wis\Config;


class Trunk
{

    /**
     * Método para capturar troncal desde la DB
     * @version 2020-04-14
     */
    public static function Get()
    {
        \Epys\Wis\Console::log("Epys\Wis\Config\Trunk::Get().");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database", "args"]);

        $trunk = false;


        // Si existe el contacto, busco su zona
        if (\Epys\Wis\Client::$contact->CODI_ZONA)
            $trunk = \Epys\Wis\Client::$database->where(["NMRO_TRONCAL" => \Epys\Wis\Client::$provider, "CODI_ZONA" => \Epys\Wis\Client::$contact->CODI_ZONA])->get("WI.WIT_TRONCAL")->result()[0];

        if (!$trunk)
            $trunk = \Epys\Wis\Client::$database->where(["NMRO_TRONCAL" => \Epys\Wis\Client::$provider, "CODI_ZONA" => "XXXX"])->get("WI.WIT_TRONCAL")->result()[0];


        //Envio Logs
        if ($trunk)
            \Epys\Wis\Console::log(([
                    NMRO_TRONCAL => $trunk->NMRO_TRONCAL,
                    CODI_ZONA => $trunk->CODI_ZONA,
                    CODI_TECNO => $trunk->CODI_TECNO,
                    IDEN_IVR => $trunk->IDEN_IVR,
                    FLAG_BLANCA => $trunk->FLAG_BLANCA,
                    ACTIVO => $trunk->ACTIVO
            ]));


        return $trunk ? $trunk : [];


    }


    /**
     * Método para crear un proveedor en la base de datos
     * @version 2020-04-14
     */
    protected static function setProvider()
    {
        \Epys\Wis\Console::log("Epys\Wis\Config\Trunk::setProvider().");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database", "args"]);

        \Epys\Wis\Client::$database->insert("WI.WIT_TRONCAL", [
            NMRO_TRONCAL => \Epys\Wis\Client::$args->message->provider,
            DESC_TRONCAL => \Epys\Wis\Client::$args->message->provider,
            CODI_ZONA => "XXXX",
            ACTIVO => 0
        ]);

    }


}
