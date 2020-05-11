<?php


namespace Epys\Wis\Bot;


class Ask
{

    /**
     * Método para responder preguntas
     * @version 2020-04-20
     */
    public static function Response()
    {
        \Epys\Wis\Console::log("Epys\Wis\Bot\Ask::Response().");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database", "args", "conversation"]);

        // Valido que el IVR tenga una acción o pregunta
        if (\Epys\Wis\Client::$conversation->CODI_ACCION) {

            $Blob = new \Epys\Wis\Util\Blob();
            eval('$Blob->run = function () { ' . \Epys\Wis\Client::$conversation->BLOB_ACCION . '};');
            $Blob->run();

        }

        self::Request(\Epys\Wis\Client::$conversation->IDEN_ACTIV);

    }

    /**
     * Método para buscar preguntas pendientes
     * @version 2020-04-20
     */
    public static function Request($iden = false)
    {
        \Epys\Wis\Console::log("Epys\Wis\Bot\Ask::Request(" . $iden . ").");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database", "args"]);

        if (!$iden) {
            // Busco Actividad pendiente asociadas al número que venía en args
            \Epys\Wis\Client::Activ();
            $iden = \Epys\Wis\Client::$activ->IDEN_ACTIV;
        }

        if($iden){
            $pregunta = self::getPregIden($iden);

            if ($pregunta)
                if (\Epys\Wis\Client::$network->check()) { // Verifico que este agregado provider y contact

                    // Guardo Pregunta
                    \Epys\Wis\Config\Conversation::setContactTrunk(["CODI_PREGUNTA" => $pregunta->CODI_PREGUNTA, "IDEN_ACTIV" => $iden]);

                    // Envio mensaje
                    \Epys\Wis\Client::$network
                        ->transac($iden)
                        ->text($pregunta->DESC_PREGUNTA)
                        ->send();

                    // Guardo mensaje
                    \Epys\Wis\Flow\Comentario::setBot($iden, $pregunta->DESC_PREGUNTA);

                }
        }


    }


    /**
     * Método para buscar preguntas pendientes por IDEN_ACTIV = IDEN_TRANSAC
     * @version 2020-04-20
     */
    public static function getPregIden($iden)
    {
        \Epys\Wis\Console::log("Epys\Wis\Bot\Ask::getPregIden(" . $iden . ").");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database"]);

        // Busco Preguntas que se ejecuten en pendiente
        $preg = \Epys\Wis\Client::$database
            ->select("P.*")
            ->where(["A.IDEN_TRANSAC"=> $iden, "P.CODI_ESTADO" => 'PEND'])
            ->where("P.CODI_PREGUNTA NOT IN (SELECT CODI_PREGUNTA FROM WI.WIT_RESPUESTA WHERE IDEN_ACTIV = A.IDEN_TRANSAC) AND P.ACTIVO = 1")
            ->join("WI.WIT_PREGXTIPO T", "T.CODI_PREGUNTA = P.CODI_PREGUNTA")
            ->join("SU.SUT_TRANSAC A", "A.IDEN_TIPOACTIV = T.IDEN_TIPOACTIV")
            ->order_by("P.NMRO_PREGUNTA")
            ->get("WI.WIT_PREGUNTA P")->result()[0];

        return $preg;

    }


    /**
     * Método para buscar preguntas pendientes por contacto
     * @version 2020-04-20
     */
    public static function getPregContact($number)
    {
        \Epys\Wis\Console::log("Epys\Wis\Bot\Ask::getPregContact(" . $number . ").");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database"]);

    }


    public
    static function codi($codi)
    {
        \Epys\Wis\Console::log("Epys\Wis\Bot\Ask::codi(" . $codi . ").");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database"]);

        return \Epys\Wis\Client::$database->where(["CODI_PREGUNTA" => $codi, "ACTIVO" => 1])
            ->get("WI.WIT_PREGUNTA")->result()[0];
    }
}
