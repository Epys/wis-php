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
        if (!\Epys\Wis\Client::$trunk->CODI_TECNO) {
            \Epys\Wis\Console::error('La troncal ' . \Epys\Wis\Client::$args->provider->number . ' no existe en nuestra base de datos. Contacte al administrador de Wis.', \Epys\Wis\Console::ERROR_INPUT);
        }

        // Valido Contacto
        \Epys\Wis\Console::log('Valido que exista contacto.');
        if (!\Epys\Wis\Client::$contact->IDEN_CONTACTO) {
            \Epys\Wis\Console::error('La contacto ' . \Epys\Wis\Client::$args->contact->number . ' no existe en nuestra base de datos. Contacte al administrador de Wis.', \Epys\Wis\Console::ERROR_INPUT);
        }

        // Conversación
        \Epys\Wis\Console::log('Valido que exista pregunta en conversación.');
        if (\Epys\Wis\Client::$conversation->CODI_PREGUNTA) {


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
