<?php


namespace Epys\Wis\Flow;

class Activtemp
{

    /**
     * Método para buscar actividades pendientes por contacto y tecno
     * @version        20.05.185.391
     */
    public static function getContactTecno()
    {
        \Epys\Wis\Console::log("Epys\Wis\Flow\Activtemp::getContactTecno().");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database"]);

        // Valido Troncal
        if (!\Epys\Wis\Client::$trunk->CODI_TECNO)
            return [];

        // Valido Contacto
        if (!\Epys\Wis\Client::$contact->IDEN_CONTACTO)
            return [];

        // Si existe actividad pendiente con la troncal
        $activ = \Epys\Wis\Client::$database->where([
            "CODI_TECNO" => \Epys\Wis\Client::$trunk->CODI_TECNO,
            "IDEN_USERING" => \Epys\Wis\Client::$contact->IDEN_CONTACTO,
            "NMRO_ACTIV" => 1
        ])->get("FD.FDT_ACTIVTEMP")->result()[0];

        //Envio Logs
        if ($activ) {
            \Epys\Wis\Console::log(([
                Activtemp => [
                    IDEN_ACTIV => $activ->IDEN_ACTIV,
                    NMRO_ACTIV => $activ->NMRO_ACTIV,
                    FECH_CREACION => $activ->FECH_CREACION,
                    CODI_TECNO => $activ->CODI_TECNO,
                    IDEN_USERING => $activ->IDEN_USERING
                ]
            ]));

            // Agrego transaccion a los argumentos
            \Epys\Wis\Client::setArgstran($activ->IDEN_ACTIV);
        }

        return $activ;

    }

    /**
     * Método para buscar actividades pendientes por contacto
     * @version        20.05.185.391
     */
    public static function getContact()
    {
        \Epys\Wis\Console::log("Epys\Wis\Flow\Activtemp::getContact().");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database", "args", "contact"]);

        // Si existe actividad pendiente con la troncal
        $activ = \Epys\Wis\Client::$database->where([
            "IDEN_USERING" => \Epys\Wis\Client::$contact->IDEN_CONTACTO,
            "NMRO_ACTIV" => 1
        ])->get("FD.FDT_ACTIVTEMP")->result()[0];

        //Envio Logs
        if ($activ) {
            // Agrego transaccion a los argumentos
            \Epys\Wis\Client::setArgstran($activ->IDEN_ACTIV);

            \Epys\Wis\Console::log(([
                Activtemp => [
                    IDEN_ACTIV => $activ->IDEN_ACTIV,
                    NMRO_ACTIV => $activ->NMRO_ACTIV,
                    FECH_CREACION => $activ->FECH_CREACION,
                    CODI_TECNO => $activ->CODI_TECNO,
                    IDEN_USERING => $activ->IDEN_USERING
                ]
            ]));

        }


        return $activ;

    }

    /**
     * Método para buscar actividades pendientes por activ
     * @version        20.05.185.391
     */
    public static function getActiv($iden)
    {
        \Epys\Wis\Console::log("Epys\Wis\Flow\Activtemp::getActiv(" . $iden . ").");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database"]);

        // Si existe actividad pendiente con la troncal
        $activ = \Epys\Wis\Client::$database->where([
            "IDEN_ACTIV" => $iden,
        ])->get("FD.FDT_ACTIVTEMP")->result()[0];

        //Envio Logs
        if ($activ) {
            // Agrego transaccion a los argumentos
            \Epys\Wis\Client::setArgstran($activ->IDEN_ACTIV);

            \Epys\Wis\Console::log(([
                Activtemp => [
                    IDEN_ACTIV => $activ->IDEN_ACTIV,
                    NMRO_ACTIV => $activ->NMRO_ACTIV,
                    FECH_CREACION => $activ->FECH_CREACION,
                    CODI_TECNO => $activ->CODI_TECNO,
                    IDEN_USERING => $activ->IDEN_USERING
                ]
            ]));

        }


        return $activ;

    }

}
