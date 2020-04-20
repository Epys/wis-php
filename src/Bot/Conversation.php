<?php


namespace Epys\Wis\Bot;


class Conversation
{

    /**
     * Método para buscar conversaciones
     * @version 2020-04-19
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
            ->join("WI.WIT_IVR I", "I.CODI_IVR = C.CODI_IVR", "left")
            ->get("WI.WIT_CONVERSACION C")->result()[0];

        //Envio Logs
        if ($conversacion)
            \Epys\Wis\Console::log(([
                FECH_CONVERSACION => $conversacion->FECH_CONVERSACION,
                CODI_PREGUNTA => $conversacion->CODI_PREGUNTA,
                IDEN_ACTIV => $conversacion->IDEN_ACTIV,
                CODI_IVR => $conversacion->CODI_IVR
            ]));

        return $conversacion;

    }

}
