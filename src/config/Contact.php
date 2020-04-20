<?php


namespace Epys\Wis\Config;


class Contact
{


    /**
     * Método para capturar contacto desde la DB
     * @version 2020-04-14
     */
    public static function Get()
    {
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Contact::Get().');

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(['database', 'args']);

        // Busco contacto
        $contacto = \Epys\Wis\Client::$database->select("*, PA.DESC_EMPRESA(CODI_EMPRESA) DESC_EMPRESA, PA.DESC_ZONA(CODI_ZONA) DESC_ZONA", false)
            ->where(["NMRO_CONTACTO" => \Epys\Wis\Client::$args->contact->number])
            ->get("WI.WIT_CONTACTO")->result()[0];

        //Envio Logs
        if ($contacto)
            \Epys\Wis\Console::log(([
                Contact => [
                    NMRO_CONTACTO => $contacto->NMRO_CONTACTO,
                    IDEN_CONTACTO => $contacto->IDEN_CONTACTO,
                    CODI_ZONA => $contacto->CODI_ZONA,
                    ACTIVO => $contacto->ACTIVO
                ]
            ]));

        return $contacto;

    }


    /**
     * Método para crear un contacto en la base de datos
     * @version 2020-04-14
     */
    private static function _setContact()
    {

        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Contact::_setContact().');

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(['database', 'args']);

        \Epys\Wis\Client::$database->insert('WI.WIT_CONTACT', [
            NMRO_CONTACTO => \Epys\Wis\Client::$args->contact->number,
            DESC_CONTACTO => \Epys\Wis\Client::$args->contact->name,
            ACTIVO => 0
        ]);

    }

}
