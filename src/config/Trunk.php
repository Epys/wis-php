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

        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Trunk::Get().');

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(['database', 'args']);

        $trunk = false;


        // Si existe el contacto, busco su zona
        if (\Epys\Wis\Client::$contact->CODI_ZONA)
            $trunk = \Epys\Wis\Client::$database->where(["NMRO_TRONCAL" => \Epys\Wis\Client::$args->provider->number, "CODI_ZONA" => \Epys\Wis\Client::$contact->CODI_ZONA])->get("WI.WIT_TRONCAL")->result()[0];

        if (!$trunk)
            $trunk = \Epys\Wis\Client::$database->where(["NMRO_TRONCAL" => \Epys\Wis\Client::$args->provider->number, "CODI_ZONA" => "XXXX"])->get("WI.WIT_TRONCAL")->result()[0];


        //Envio Logs
        if ($trunk)
            \Epys\Wis\Console::log(([
                Trunk => [
                    NMRO_TRONCAL => $trunk->NMRO_TRONCAL,
                    CODI_ZONA => $trunk->CODI_ZONA,
                    CODI_TECNO => $trunk->CODI_TECNO,
                    IDEN_IVR => $trunk->IDEN_IVR,
                    FLAG_BLANCA => $trunk->FLAG_BLANCA,
                    ACTIVO => $trunk->ACTIVO
                ]
            ]));


        return $trunk;


    }


    /**
     * Método para crear un proveedor en la base de datos
     * @version 2020-04-14
     */
    private static function _setProvider()
    {

        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Trunk::_setProvider().');

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(['database', 'args']);

        \Epys\Wis\Client::$database->insert('WI.WIT_TRONCAL', [
            NMRO_TRONCAL => \Epys\Wis\Client::$args->provider->number,
            DESC_TRONCAL => \Epys\Wis\Client::$args->provider->number,
            CODI_ZONA => 'XXXX',
            ACTIVO => 0
        ]);

    }


}
