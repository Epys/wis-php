<?php


namespace Epys\Wis\Bot;


class At
{


    /**
     * Método para responder preguntas
     * @version        20.05.185.391
     */
    public static function Response()
    {
        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database", "args", "trunk", "contact"]);

        $text = strtolower(trim(\Epys\Wis\Client::$args->message->content->text));

        if (strpos($text, '@') === 0) {
            \Epys\Wis\Console::log("Epys\Wis\Bot\At::Response(" . $text . ").");

            $arrobas = \Epys\Wis\Client::$database
                ->join("WI.WIT_ARROXTRON T", "A.IDEN_ARROBA = T.IDEN_ARROBA AND T.IDEN_TRONCAL =" . \Epys\Wis\Client::$trunk->IDEN_TRONCAL, "left")
                ->get("WI.WIT_ARROBA A")
                ->result();

            if ($arrobas)
                foreach ($arrobas as $arroba) {
                    if (preg_match($arroba->RGXP_MATCH, $text) === 1) {
                        \Epys\Wis\Console::log($arroba->RGXP_MATCH . " <> " . $text);

                        // Valido que la troncal pueda ejecutar este comando
                        if (!$arroba->IDEN_TRONCAL) {
                            if (\Epys\Wis\Client::$network->check())
                                \Epys\Wis\Client::$network->text("La troncal no esta autorizada para ejecutar *" . $text . "*.")->send();
                            exit();
                        }

                        // Si existe BLOB
                        if ($arroba->BLOB_ARROBA) {
                            $Blob = new \Epys\Wis\Util\Blob();
                            eval('$Blob->run = function ($arroba) { ' . $arroba->BLOB_ARROBA . '};');
                            $Blob->run($arroba);
                        }

                        // Si existe pregunta
                        if ($arroba->CODI_PREGUNTA) {
                            $pregunta = \Epys\Wis\Bot\Ask::codi($arroba->CODI_PREGUNTA);
                            if ($pregunta)
                                if (\Epys\Wis\Client::$network->check()) { // Verifico que este agregado provider y contact

                                    // Guardo Pregunta
                                    \Epys\Wis\Config\Conversation::setContactTrunk(["CODI_PREGUNTA" => $pregunta->CODI_PREGUNTA]);

                                    // Envio mensaje
                                    \Epys\Wis\Client::$network
                                        ->text($pregunta->DESC_PREGUNTA)
                                        ->send();
                                }
                        }

                        // Verifico si tengo que dar exits
                        if ($arroba->FLAG_EXIT)
                            exit();

                    }
                }
        }


    }


}
