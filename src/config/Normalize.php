<?php


namespace Epys\Wis\Config;

class Normalize
{


    /**
     * Método para capturar input de PHP
     * @version 2020-04-14
     */
    public static function Input()
    {

        \Epys\Wis\Console::log('Capturo datos ´php://input´.');

        // Capturo datos
        $args = json_decode(file_get_contents('php://input'), FALSE);

        // Verifico que los datos recepcionados vengan en formato JSON
        if (json_last_error() !== JSON_ERROR_NONE)
            \Epys\Wis\Console::error('La entrada de datos no es JSON.', \Epys\Wis\Console::ERROR_VALIDATION_JSON);

        // Si no hay ninguna dato de entrada
        if (!$args)
            \Epys\Wis\Console::error('La entrada de datos esta vacía.', \Epys\Wis\Console::ERROR_VALIDATION_EMPTY);

        // Guardo datos recepcionados en logs
        \Epys\Wis\Console::input($args);

        // Valido la estructura
        self::Validate($args);

        return $args;

    }

    /**
     * Método para Validar
     * @version 2020-04-14
     */
    public static function Validate($args)
    {

        \Epys\Wis\Console::log('Validación de la estructura.');
        self::_interface($args);
        \Epys\Wis\Console::log('Validación correcta.');
    }

    /**
     * Método para validar la estructura
     * @param $args Argumento recivido en POST
     * @version 2020-04-14
     */
    private static function _interface($args)
    {


        if (!$args->id)
            \Epys\Wis\Console::error('No existe objeto ID.', \Epys\Wis\Console::ERROR_INPUT_ID);

        if (!in_array($args->network, ['whatsapp', 'messenger', 'telegram', 'instagram']))
            \Epys\Wis\Console::error('No existe objeto NETWORK.', \Epys\Wis\Console::ERROR_INPUT_NETWORK);

        if (!$args->time)
            \Epys\Wis\Console::error('No existe objeto TIME.', \Epys\Wis\Console::ERROR_INPUT_TIME);

        switch ($args->type) {
            case 'message':
                self::_message($args);
                break;
            case 'dlv':
                self::_dlv($args);
                break;
            default:
                \Epys\Wis\Console::error('El objeto TYPE no es valido.', \Epys\Wis\Console::ERROR_INPUT_TYPE);
                break;
        }
    }

    /**
     * Método para validar la estructura del mensaje
     * @param $args Argumento recivido en POST
     * @version 2020-04-14
     */
    private static function _message($args)
    {

        if (!$args->contact->number)
            \Epys\Wis\Console::error('No existe objeto CONTACT.NUMBER.', \Epys\Wis\Console::ERROR_REQUIRED);

        if (!$args->provider->number)
            \Epys\Wis\Console::error('No existe objeto PROVIDER.NUMBER.', \Epys\Wis\Console::ERROR_REQUIRED);

        if (!in_array($args->direction, ['sent', 'received']))
            \Epys\Wis\Console::error('El objeto DIRECTION no es valido.', \Epys\Wis\Console::ERROR_INPUT_DIRECTION);


        switch ($args->content->type) {
            case 'text':
                if (!$args->content->text)
                    \Epys\Wis\Console::error('El objeto CONTENT.TEXT no es valido.', \Epys\Wis\Console::ERROR_INPUT_CONTENT_TEXT);
                break;
            case 'image':
                if (!$args->content->url)
                    \Epys\Wis\Console::error('El objeto CONTENT.IMAGE no es valido.', \Epys\Wis\Console::ERROR_INPUT_CONTENT_IMAGE);
                break;
            case 'sticker':
                if (!$args->content->url)
                    \Epys\Wis\Console::error('El objeto CONTENT.STICKER no es valido.', \Epys\Wis\Console::ERROR_INPUT_CONTENT_STICKER);
                break;
            case 'audio':
                if (!$args->content->url)
                    \Epys\Wis\Console::error('El objeto CONTENT.AUDIO no es valido.', \Epys\Wis\Console::ERROR_INPUT_CONTENT_AUDIO);
                break;
            case 'video':
                if (!$args->content->url)
                    \Epys\Wis\Console::error('El objeto CONTENT.VIDEO no es valido.', \Epys\Wis\Console::ERROR_INPUT_CONTENT_VIDEO);
                break;
            case 'document':
                if (!$args->content->url)
                    \Epys\Wis\Console::error('El objeto CONTENT.DOCUMENT no es valido.', \Epys\Wis\Console::ERROR_INPUT_CONTENT_DOCUMENT);
                break;
            case 'location':
                if (!$args->content->longitude || $args->content->latitude)
                    \Epys\Wis\Console::error('El objeto CONTENT.LOCATION no es valido.', \Epys\Wis\Console::ERROR_INPUT_CONTENT_LOCATION);
                break;
            default:
                \Epys\Wis\Console::error('El objeto CONTENT no es valido.', \Epys\Wis\Console::ERROR_INPUT_CONTENT);
                break;
        }

    }


    /**
     * Método para validar la estructura del dlv
     * @param $args Argumento recivido en POST
     * @version 2020-04-14
     */
    private static function _dlv($args)
    {

        if (!$args->dlvStatus->time)
            \Epys\Wis\Console::error('No existe objeto DLVSTATUS.TIME.', \Epys\Wis\Console::ERROR_REQUIRED);

        if (!$args->dlvStatus->dlv)
            \Epys\Wis\Console::error('No existe objeto DLVSTATUS.DLV.', \Epys\Wis\Console::ERROR_REQUIRED);


    }

}

