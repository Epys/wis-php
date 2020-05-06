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
        \Epys\Wis\Console::log("Epys\Wis\Config\Contact::Get().");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database", "args"]);

        // Busco contacto
        $contacto = \Epys\Wis\Client::$database->select("*, PA.DESC_EMPRESA(CODI_EMPRESA) DESC_EMPRESA, PA.DESC_ZONA(CODI_ZONA) DESC_ZONA", false)
            ->where(["NMRO_CONTACTO" => \Epys\Wis\Client::$args->message->contact])
            ->get("WI.WIT_CONTACTO")->result()[0];

        //Envio Logs
        if ($contacto)
            \Epys\Wis\Console::log(([
                    NMRO_CONTACTO => $contacto->NMRO_CONTACTO,
                    IDEN_CONTACTO => $contacto->IDEN_CONTACTO,
                    CODI_ZONA => $contacto->CODI_ZONA,
                    ACTIVO => $contacto->ACTIVO
            ]));

        return $contacto ? $contacto : [];

    }


    /**
     * Método para crear un contacto en la base de datos
     * @version 2020-04-14
     */
    protected static function setContact()
    {
        \Epys\Wis\Console::log("Epys\Wis\Config\Contact::setContact().");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database", "args"]);

        \Epys\Wis\Client::$database->insert("WI.WIT_CONTACT", [
            NMRO_CONTACTO => \Epys\Wis\Client::$args->message->contact,
            DESC_CONTACTO => \Epys\Wis\Client::$args->message->contact->name,
            ACTIVO => 0
        ]);

    }

}
