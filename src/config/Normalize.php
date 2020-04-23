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
        \Epys\Wis\Console::log('Epys\Wis\Config\Normalize::Input().');

        // Capturo datos
        $args = json_decode(file_get_contents('php://input'), FALSE);

        // Verifico que los datos recepcionados vengan en formato JSON
        if (json_last_error() !== JSON_ERROR_NONE)
            \Epys\Wis\Console::error('La entrada de datos no es JSON.', \Epys\Wis\Console::ERROR_VALIDATION_JSON,__CLASS__,__LINE__);

        // Si no hay ninguna dato de entrada
        if (!$args)
            \Epys\Wis\Console::error('La entrada de datos esta vacía.', \Epys\Wis\Console::ERROR_VALIDATION_EMPTY,__CLASS__,__LINE__);

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
        \Epys\Wis\Console::log('Epys\Wis\Config\Normalize::Validate().');
        self::type($args);

    }

    /**
     * Método para validar la estructura
     * @param $args Argumento recivido en POST
     * @version 2020-04-14
     */
    protected static function type($args)
    {
        \Epys\Wis\Console::log('Epys\Wis\Config\Normalize::type().');

        if (!$args->id)
            \Epys\Wis\Console::error('No existe objeto ID.', \Epys\Wis\Console::ERROR_INPUT_ID,__CLASS__,__LINE__);

        if (!in_array($args->network, ['whatsapp', 'messenger', 'telegram', 'instagram']))
            \Epys\Wis\Console::error('No existe objeto NETWORK.', \Epys\Wis\Console::ERROR_INPUT_NETWORK,__CLASS__,__LINE__);

        if (!$args->time)
            \Epys\Wis\Console::error('No existe objeto TIME.', \Epys\Wis\Console::ERROR_INPUT_TIME,__CLASS__,__LINE__);

        switch ($args->type) {
            case 'message':
                self::message($args);
                break;
            case 'dlv':
                self::dlv($args);
                break;
            default:
                \Epys\Wis\Console::error('El objeto TYPE no es valido.', \Epys\Wis\Console::ERROR_INPUT_TYPE,__CLASS__,__LINE__);
                break;
        }
    }

    /**
     * Método para validar la estructura del mensaje
     * @param $args Argumento recivido en POST
     * @version 2020-04-14
     */
    protected static function message($args)
    {
        \Epys\Wis\Console::log('Epys\Wis\Config\Normalize::message().');

        if (!$args->contact->number)
            \Epys\Wis\Console::error('No existe objeto CONTACT.NUMBER.', \Epys\Wis\Console::ERROR_REQUIRED,__CLASS__,__LINE__);

        if (!$args->provider->number)
            \Epys\Wis\Console::error('No existe objeto PROVIDER.NUMBER.', \Epys\Wis\Console::ERROR_REQUIRED,__CLASS__,__LINE__);

        if (!in_array($args->direction, ['sent', 'received']))
            \Epys\Wis\Console::error('El objeto DIRECTION no es valido.', \Epys\Wis\Console::ERROR_INPUT_DIRECTION,__CLASS__,__LINE__);


        switch ($args->content->type) {
            case 'text':
                if (!isset($args->content->text))
                    \Epys\Wis\Console::error('El objeto CONTENT.TEXT no es valido.', \Epys\Wis\Console::ERROR_INPUT_CONTENT_TEXT,__CLASS__,__LINE__);
                break;
            case 'image':
                if (!isset($args->content->url))
                    \Epys\Wis\Console::error('El objeto CONTENT.IMAGE no es valido.', \Epys\Wis\Console::ERROR_INPUT_CONTENT_IMAGE,__CLASS__,__LINE__);
                break;
            case 'sticker':
                if (!isset($args->content->url))
                    \Epys\Wis\Console::error('El objeto CONTENT.STICKER no es valido.', \Epys\Wis\Console::ERROR_INPUT_CONTENT_STICKER,__CLASS__,__LINE__);
                break;
            case 'audio':
                if (!isset($args->content->url))
                    \Epys\Wis\Console::error('El objeto CONTENT.AUDIO no es valido.', \Epys\Wis\Console::ERROR_INPUT_CONTENT_AUDIO,__CLASS__,__LINE__);
                break;
            case 'video':
                if (!isset($args->content->url))
                    \Epys\Wis\Console::error('El objeto CONTENT.VIDEO no es valido.', \Epys\Wis\Console::ERROR_INPUT_CONTENT_VIDEO,__CLASS__,__LINE__);
                break;
            case 'document':
                if (!isset($args->content->url))
                    \Epys\Wis\Console::error('El objeto CONTENT.DOCUMENT no es valido.', \Epys\Wis\Console::ERROR_INPUT_CONTENT_DOCUMENT,__CLASS__,__LINE__);
                break;
            case 'location':
                if (!isset($args->content->longitude) || isset($args->content->latitude))
                    \Epys\Wis\Console::error('El objeto CONTENT.LOCATION no es valido.', \Epys\Wis\Console::ERROR_INPUT_CONTENT_LOCATION,__CLASS__,__LINE__);
                break;
            default:
                \Epys\Wis\Console::error('El objeto CONTENT no es valido.', \Epys\Wis\Console::ERROR_INPUT_CONTENT,__CLASS__,__LINE__);
                break;
        }

    }


    /**
     * Método para validar la estructura del dlv
     * @param $args Argumento recivido en POST
     * @version 2020-04-14
     */
    protected static function dlv($args)
    {
        \Epys\Wis\Console::log('Epys\Wis\Config\Normalize::dlv().');

        if (!$args->dlvStatus->time)
            \Epys\Wis\Console::error('No existe objeto DLVSTATUS.TIME.', \Epys\Wis\Console::ERROR_REQUIRED,__CLASS__,__LINE__);

        if (!$args->dlvStatus->dlv)
            \Epys\Wis\Console::error('No existe objeto DLVSTATUS.DLV.', \Epys\Wis\Console::ERROR_REQUIRED,__CLASS__,__LINE__);


    }

}

