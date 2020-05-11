<?php


namespace Epys\Wis\Bot;


class Action
{


    public
    static function codi($codi)
    {
        \Epys\Wis\Console::log("Epys\Wis\Bot\Action::codi(" . $codi . ").");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database"]);

        return \Epys\Wis\Client::$database->where(["CODI_ACCION" => $codi, "ACTIVO" => 1])
            ->get("WI.WIT_ACCION")->result()[0];
    }


    public
    static function blob($codi)
    {
        \Epys\Wis\Console::log("Epys\Wis\Bot\Action::blob(" . $codi . ").");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database"]);

        return (self::codi($codi))->BLOB_ACCION;

    }

}
