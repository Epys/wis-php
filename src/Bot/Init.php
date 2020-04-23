<?php


namespace Epys\Wis\Bot;


class Init
{

    /**
     * Create a new Bot.
     */
    public function __construct()
    {

        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Bot::Init().');

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(['database', 'args']);

        // Busco Actividad pendiente
        \Epys\Wis\Client::Activ();

        // Busco Conversaciones pendientes
        \Epys\Wis\Client::Conversation();

        // Seteo tipo de mensaje
        switch (\Epys\Wis\Client::$args->direction) {
            case 'received':
                self::received();
                break;
            case 'sent':
                self::sent();
                break;
        }

    }

    /**
     * Método para cuando recibo un mensaje de entrada
     * @version 2020-04-19
     */
    protected static function received()
    {

        // Valido Troncal
        \Epys\Wis\Console::log('Valido que exista troncal.');
        if (!\Epys\Wis\Client::$trunk->NMRO_TRONCAL) {
            // Envio mensaje
            \Epys\Wis\Client::$network
                ->provider(\Epys\Wis\Client::$args->provider->number)
                ->contact(\Epys\Wis\Client::$args->contact->number)
                ->text('La troncal +' . \Epys\Wis\Client::$args->provider->number . ' no existe en nuestra base de datos. Contacte al administrador de Wis.')
                ->send();
            \Epys\Wis\Console::error('La troncal +' . \Epys\Wis\Client::$args->provider->number . ' no existe en nuestra base de datos. Contacte al administrador de Wis.', \Epys\Wis\Console::ERROR_INPUT);
        }

        // Valido Contacto
        \Epys\Wis\Console::log('Valido que exista contacto.');
        if (!\Epys\Wis\Client::$contact->IDEN_CONTACTO) {

            // Envio mensaje
            \Epys\Wis\Client::$network
                ->provider(\Epys\Wis\Client::$args->provider->number)
                ->contact(\Epys\Wis\Client::$args->contact->number)
                ->text('El contacto +' . \Epys\Wis\Client::$args->contact->number . ' no existe en nuestra base de datos. Contacte al administrador de Wis.')
                ->send();

            \Epys\Wis\Console::error('El contacto ' . \Epys\Wis\Client::$args->contact->number . ' no existe en nuestra base de datos. Contacte al administrador de Wis.', \Epys\Wis\Console::ERROR_INPUT);
        }

        // Verifico si hay actividades Pendientes
        if (\Epys\Wis\Client::$activ->IDEN_ACTIV) {
            // Verifico pregunta pendiente
            \Epys\Wis\Console::log('Valido que exista pregunta en conversación.');
            if (\Epys\Wis\Client::$conversation->CODI_PREGUNTA) {
                // Verifico pregunta pendiente
                \Epys\Wis\Bot\Ask::Activ();
            } else {
                // Guardo comentario
            }
        } else {
            // Verifico pregunta pendiente
            \Epys\Wis\Console::log('Valido que exista pregunta en conversación.');
            if (\Epys\Wis\Client::$conversation->CODI_PREGUNTA) {
                // Verifico pregunta pendiente
                \Epys\Wis\Bot\Ask::Fina();
            } else {
                // Genero IVR
                \Epys\Wis\Bot\Ivr::Init();
            }
        }

    }

    /**
     * Método para cuando recibo un mensaje de salida
     * @version 2020-04-19
     */
    protected static function sent()
    {


    }
}
