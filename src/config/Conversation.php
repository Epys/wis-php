<?php


namespace Epys\Wis\Config;


class Conversation
{

    /**
     * Método para buscar conversaciones
     * @version 2020-04-19
     */
    public static function getContactTrunk()
    {
        \Epys\Wis\Console::log("Epys\Wis\Config\Conversation::getContactTrunk().");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database"]);

        // Valido Troncal
        if (!\Epys\Wis\Client::$trunk->CODI_TECNO)
            return;

        // Valido Contacto
        if (!\Epys\Wis\Client::$contact->IDEN_CONTACTO)
            return;

        // Busco si hay alguna pregunta en el la tabla conversación
        $conversacion = \Epys\Wis\Client::$database
            ->select("*")
            ->where([
                "NMRO_CONTACTO" => \Epys\Wis\Client::$contact->NMRO_CONTACTO,
                "NMRO_TRONCAL" => \Epys\Wis\Client::$trunk->NMRO_TRONCAL,
            ])
            ->get("WI.WIT_CONVERSACION C")->result()[0];

        // Asigno IVR actual
        if ($conversacion->IDEN_IVR) {
            $ivr = \Epys\Wis\Bot\Ivr::iden($conversacion->IDEN_IVR);
            \Epys\Wis\Client::setIvr($ivr);
            foreach ($ivr as $k => $v) $conversacion->$k = $v;
        }

        // Asigno Pregunta actual
        if ($conversacion->CODI_PREGUNTA) {
            $preg = \Epys\Wis\Bot\Ask::codi($conversacion->CODI_PREGUNTA);
            \Epys\Wis\Client::setAsk($preg);
            foreach ($preg as $k => $v) $conversacion->{$k} = $v;

            if ($preg->CODI_ACCION)
                $acc = \Epys\Wis\Bot\Action::codi($preg->CODI_ACCION);
            foreach ($acc as $k => $v) $conversacion->{$k} = $v;
        }

        //Envio Logs
        if ($conversacion)
            \Epys\Wis\Console::log((array_filter([
                IDEN_CONVERSACION => $conversacion->IDEN_CONVERSACION,
                FECH_CONVERSACION => $conversacion->FECH_CONVERSACION,
                CODI_PREGUNTA => $conversacion->CODI_PREGUNTA,
                IDEN_ACTIV => $conversacion->IDEN_ACTIV,
                IDEN_IVR => $conversacion->IDEN_IVR,
                CODI_IVR => $conversacion->CODI_IVR,
                CODI_ACCION => $conversacion->CODI_IVR
            ])));

        return $conversacion;

    }


    /**
     * Método para limpiar
     * @version 2020-04-19
     */
    public static function delContactTrunk()
    {
        \Epys\Wis\Console::log("Epys\Wis\Config\Conversation::delContactTrunk().");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database", "contact", "trunk"]);

        // Busco si hay alguna pregunta en el la tabla conversación
        return \Epys\Wis\Client::$database
            ->delete("WI.WIT_CONVERSACION", [
                "NMRO_CONTACTO" => \Epys\Wis\Client::$contact->NMRO_CONTACTO,
                "NMRO_TRONCAL" => \Epys\Wis\Client::$trunk->NMRO_TRONCAL,
            ]);

    }

    /**
     * Método para crear
     * @version 2020-04-19
     */
    public static function setContactTrunk($option = ["IDEN_IVR", "CODI_PREGUNTA", "IDEN_ACTIV"])
    {
        \Epys\Wis\Console::log("Epys\Wis\Config\Conversation::setContactTrunk().");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database", "contact", "trunk"]);

        // Busco si hay alguna pregunta en el la tabla conversación
        return \Epys\Wis\Client::$database
            ->replace("WI.WIT_CONVERSACION", [
                "NMRO_CONTACTO" => \Epys\Wis\Client::$contact->NMRO_CONTACTO,
                "NMRO_TRONCAL" => \Epys\Wis\Client::$trunk->NMRO_TRONCAL,
                "IDEN_IVR" => $option["IDEN_IVR"],
                "CODI_PREGUNTA" => $option["CODI_PREGUNTA"],
                "IDEN_ACTIV" => $option["IDEN_ACTIV"]
            ]);


    }

}
