<?php


namespace Epys\Wis\Bot;


class Init
{

    /**
     * Create a new Bot.
     */
    public function __construct()
    {
        \Epys\Wis\Console::log("Epys\Wis\Bot\Init().");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database", "args"]);

        // Seteo tipo de mensaje
        switch (\Epys\Wis\Client::$args->message->direction) {
            case "received":
                self::received();
                break;
            case "sent":
                self::sent();
                break;
        }

    }

    /**
     * Método para cuando recibo un mensaje de entrada
     * @version        20.11.302.503
     */
    protected static function received()
    {
        \Epys\Wis\Console::log("Epys\Wis\Bot\Init::received().");

        // Busco Actividad pendiente
        \Epys\Wis\Client::Activ();

        // Busco Arrobas
        \Epys\Wis\Bot\At::Response();

        // Busco Conversaciones pendientes
        \Epys\Wis\Client::Conversation();

        // Valido Troncal
        if (!\Epys\Wis\Client::$trunk->NMRO_TRONCAL) {
            \Epys\Wis\Client::$network
                ->provider(\Epys\Wis\Client::$args->message->provider)
                ->contact(\Epys\Wis\Client::$args->message->contact)
                ->text("La troncal +" . \Epys\Wis\Client::$args->message->provider . " no existe en nuestra base de datos. Contacte al administrador de Wis.")
                ->send();
            \Epys\Wis\Console::error("La troncal +" . \Epys\Wis\Client::$args->message->provider . " no existe en nuestra base de datos. Contacte al administrador de Wis.", \Epys\Wis\Console::ERROR_INPUT);
        }

        // Valido Contacto
        if (!\Epys\Wis\Client::$contact->IDEN_CONTACTO) {
            \Epys\Wis\Client::$network
                ->provider(\Epys\Wis\Client::$args->message->provider)
                ->contact(\Epys\Wis\Client::$args->message->contact)
                ->text("El contacto +" . \Epys\Wis\Client::$args->message->contact . " no existe en nuestra base de datos. Contacte al administrador de Wis.")
                ->send();
            \Epys\Wis\Console::error("El contacto " . \Epys\Wis\Client::$args->message->contact . " no existe en nuestra base de datos. Contacte al administrador de Wis.", \Epys\Wis\Console::ERROR_INPUT);
        }

        // Verifico si hay actividades Pendientes
        if (\Epys\Wis\Client::$activ->IDEN_ACTIV) {

            \Epys\Wis\Console::log("Epys\Wis\Bot\Init::IDEN_ACTIV[" . \Epys\Wis\Client::$activ->IDEN_ACTIV . "].");
            // Veo si es una respuesta de pregunta
            \Epys\Wis\Bot\Ask::Response();
            // Guardo comentario
            \Epys\Wis\Flow\Comentario::setComentario();

        } else {

            if (\Epys\Wis\Client::$conversation->CODI_PREGUNTA) {
                // Respondo pregunta
                \Epys\Wis\Bot\Ask::Response(\Epys\Wis\Client::$conversation->IDEN_ACTIV);
            } else {

                // Genero IVR
                \Epys\Wis\Bot\Ivr::Init();

            }
        }

    }

    /**
     * Método para cuando recibo un mensaje de salida
     * @version        20.11.302.503
     */
    protected static function sent()
    {
        \Epys\Wis\Console::log("Epys\Wis\Bot\Init::sent().");

    }
}
