<?php


namespace Epys\Wis\Bot;


class Conversation
{

    /**
     * Método para buscar conversaciones
     * @version        20.11.302.503
     */
    public static function getContactTrunk()
    {
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Conversation::getContactTrunk().');

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(['database', 'contact', 'trunk']);

        // Busco si hay alguna pregunta en el la tabla conversación
        $conversacion = \Epys\Wis\Client::$database
            ->where([
                "NMRO_CONTACTO" => \Epys\Wis\Client::$contact->NMRO_CONTACTO,
                "NMRO_TRONCAL" => \Epys\Wis\Client::$trunk->NMRO_TRONCAL,
            ])
            ->join("WI.WIT_PREGUNTA P", "P.CODI_PREGUNTA = C.CODI_PREGUNTA", "left")
            ->join("WI.WIT_IVR I", "I.IDEN_IVR = C.IDEN_IVR", "left")
            ->get("WI.WIT_CONVERSACION C")->result()[0];

        //Envio Logs
        if ($conversacion)
            \Epys\Wis\Console::log((array_filter([
                IDEN_CONVERSACION => $conversacion->IDEN_CONVERSACION,
                FECH_CONVERSACION => $conversacion->FECH_CONVERSACION,
                CODI_PREGUNTA => $conversacion->CODI_PREGUNTA,
                IDEN_ACTIV => $conversacion->IDEN_ACTIV,
                IDEN_IVR => $conversacion->IDEN_IVR,
                CODI_IVR => $conversacion->CODI_IVR
            ])));

        // Asigno IVR actual
        if ($conversacion->IDEN_IVR) {
            unset($conversacion->IDEN_CONVERSACION, $conversacion->FECH_CONVERSACION, $conversacion->NMRO_CONTACTO, $conversacion->NMRO_TRONCAL, $conversacion->CODI_PREGUNTA, $conversacion->IDEN_ACTIV);
            \Epys\Wis\Client::setIvr($conversacion);
        }

        // Asigno Pregunta actual
        if ($conversacion->CODI_PREGUNTA) {
            unset($conversacion->IDEN_CONVERSACION, $conversacion->FECH_CONVERSACION, $conversacion->NMRO_CONTACTO, $conversacion->NMRO_TRONCAL, $conversacion->IDEN_IVR);
            \Epys\Wis\Client::setAsk($conversacion);
        }


        return $conversacion;

    }


    /**
     * Método para limpiar
     * @version        20.11.302.503
     */
    public static function delContactTrunk()
    {
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Conversation::delContactTrunk().');

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(['database', 'contact', 'trunk']);

        // Busco si hay alguna pregunta en el la tabla conversación
        return \Epys\Wis\Client::$database
            ->delete("WI.WIT_CONVERSACION", [
                "NMRO_CONTACTO" => \Epys\Wis\Client::$contact->NMRO_CONTACTO,
                "NMRO_TRONCAL" => \Epys\Wis\Client::$trunk->NMRO_TRONCAL,
            ]);

    }

    /**
     * Método para crear
     * @version        20.11.302.503
     */
    public static function setContactTrunk($option = ["IDEN_IVR" => null, "CODI_PREGUNTA" => null, "IDEN_ACTIV" => null])
    {
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Conversation::setContactTrunk().');

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(['database', 'contact', 'trunk']);

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
