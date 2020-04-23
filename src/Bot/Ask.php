<?php


namespace Epys\Wis\Bot;


use Epys\Wis\Config\Conversation;

class Ask
{


    /**
     * Método para buscar preguntas pendientes
     * @version 2020-04-20
     */
    public static function Activ()
    {
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Ask::Activ().');

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(['database', 'args', 'activ', 'conversation']);

        // Valido que el IVR tenga una acción o pregunta
        if (\Epys\Wis\Client::$conversation->CODI_ACCION) {

            $Action = new \Epys\Wis\Bot\Action();
            eval('$Action->run = function () { ' . $Action::blob(\Epys\Wis\Client::$conversation->CODI_ACCION) . '};');
            $Action->run();
        }

    }

    /**
     * Método para buscar preguntas pendientes
     * @version 2020-04-20
     */
    public static function Pend($iden = false)
    {
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Ask::Pend().');

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(['database', 'args']);

        if (!$iden) {
            // Busco Actividad pendiente asociadas al número que venía en args
            \Epys\Wis\Client::Activ();
            $iden = \Epys\Wis\Client::$activ->IDEN_ACTIV;

            if (!$iden)
                \Epys\Wis\Console::log('No hay actividad pendiente.');
        }

        $pregunta = self::getPregActiv($iden);

        if ($pregunta)
            if (\Epys\Wis\Client::$network->check()) { // Verifico que este agregado provider y contact

                // Guardo Pregunta
                \Epys\Wis\Config\Conversation::setContactTrunk(["CODI_PREGUNTA" => $pregunta->CODI_PREGUNTA, "IDEN_ACTIV" => $iden]);

                // Envio mensaje
                \Epys\Wis\Client::$network
                    ->transac($iden)
                    ->text($pregunta->DESC_PREGUNTA)
                    ->send();

            }

    }


    /**
     * Método para cuando no hay actividades
     * @version 2020-04-20
     */
    public static function Fina()
    {
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Ask::Fina().');

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(['database', 'args']);

    }


    /**
     * Método para buscar preguntas pendientes por activ
     * @version 2020-04-20
     */
    public static function getPregActiv($iden)
    {

        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Ask::getPregActiv(' . $iden . ').');

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(['database']);

        $preg = \Epys\Wis\Client::$database
            ->select("P.*")
            ->where("A.IDEN_ACTIV", $iden)
            ->where("P.CODI_PREGUNTA NOT IN (SELECT CODI_PREGUNTA FROM WI.WIT_RESPUESTA WHERE IDEN_ACTIV = A.IDEN_ACTIV) AND P.ACTIVO = 1")
            ->join("WI.WIT_PREGXTIPO T", "T.CODI_PREGUNTA = P.CODI_PREGUNTA")
            ->join("FD.FDT_ACTIVTEMP A", "A.IDEN_TIPOACTIV = T.IDEN_TIPOACTIV")
            ->order_by("P.NMRO_PREGUNTA")
            ->get("WI.WIT_PREGUNTA P")->result()[0];

        //Envio Logs
        \Epys\Wis\Console::log($preg);

        return $preg;

    }

    /**
     * Método para buscar preguntas pendientes por contacto
     * @version 2020-04-20
     */
    public static function getPregContact($number)
    {
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Ask::getPregContact(' . $number . ').');

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(['database']);

    }


}
