<?php


namespace Epys\Wis\Config;

use DateTime;

class Normalize
{


    protected static $type;
    protected static $provider;
    protected static $contact;
    protected static $args;

    /**
     * Método para capturar input de PHP
     * @version        20.05.185.391
     */
    public static function Input()
    {
        \Epys\Wis\Console::log("Epys\Wis\Config\Normalize::Input().");

        // Capturo datos
        $args = json_decode(file_get_contents("php://input"), FALSE);

        // Verifico que los datos recepcionados vengan en formato JSON
        if (json_last_error() !== JSON_ERROR_NONE)
            \Epys\Wis\Console::error("La entrada de datos no es JSON (" . json_last_error() . ").", \Epys\Wis\Console::ERROR_VALIDATION_JSON, __CLASS__, __LINE__);

        // Si no hay ninguna dato de entrada
        if (!$args)
            \Epys\Wis\Console::error("La entrada de datos esta vacía.", \Epys\Wis\Console::ERROR_VALIDATION_EMPTY, __CLASS__, __LINE__);

        // Guardo datos recepcionados en logs
        \Epys\Wis\Console::input($args);

        self::$args = $args;

        // Valido la estructura
        self::Validate($args);

        return self::$args;

    }

    /**
     * Método para Validar
     * @version        20.05.185.391
     */
    public static function Validate($args)
    {
        \Epys\Wis\Console::log("Epys\Wis\Config\Normalize::Validate().");

        // Verifico si existe network
        foreach ($args as $network => $objs) {
            if (in_array($network, ["whatsapp", "messenger", "telegram", "instagram"])) {
                switch ($network) {
                    case "whatsapp":
                        self::whatsapp($objs);
                        if (self::$type == "message") {
                            \Epys\Wis\Client::setNetwork("whatsapp", ["provider" => self::$provider, "contact" => self::$contact]);
                            if (self::$provider)
                                \Epys\Wis\Client::setProvider(self::$provider);
                        }
                        break;
                }
            } else {
                \Epys\Wis\Console::error("No existe objeto NETWORK.", \Epys\Wis\Console::ERROR_REQUIRED, __CLASS__, __LINE__);
            }
        }

    }

    /**
     * Método para validar la estructura
     * @param $args Argumento recivido en POST
     * @version        20.05.185.391
     */
    protected static function whatsapp($objs)
    {
        \Epys\Wis\Console::log("Epys\Wis\Config\Normalize::whatsapp().");

        // Varifico que el Objeto sea telefono
        foreach ($objs as $provider => $payloads) {
            if (!is_numeric($provider))
                \Epys\Wis\Console::error("No existe objeto PROVIDER.", \Epys\Wis\Console::ERROR_REQUIRED, __CLASS__, __LINE__);

            // Defino Proveedor
            self::$provider = $provider;

            if (is_array($payloads)) {
                foreach ($payloads as $payload)
                    self::whatsappPayload($payload);
            } else {
                self::whatsappPayload($payloads);
                // Defino Contacto
                if ($payloads->contact->number)
                    self::$contact = $payloads->contact->number;
            }

        }

    }

    /**
     * Método para validar la estructura
     * @param $args Argumento recivido en POST
     * @version        20.05.185.391
     */
    protected static function whatsappPayload($payload)
    {
        \Epys\Wis\Console::log("Epys\Wis\Config\Normalize::whatsappPayload().");


        if (!$payload->message && !$payload->delivery)
            \Epys\Wis\Console::error('El esquema iMessageWhatsappPayload no es valido.', \Epys\Wis\Console::ERROR_REQUIRED, __CLASS__, __LINE__);

        if ($payload->message && $payload->delivery)
            \Epys\Wis\Console::error('El esquema iMessageWhatsappPayload no es valido.', \Epys\Wis\Console::ERROR_REQUIRED, __CLASS__, __LINE__);

        if ($payload->message && !$payload->contact->number)
            \Epys\Wis\Console::error('El esquema iMessageWhatsappPayload no es valido.', \Epys\Wis\Console::ERROR_REQUIRED, __CLASS__, __LINE__);

        if ($payload->message) {
            self::$type = "message";
            self::whatsappPayloadMessage($payload->message);

            $payload->message->provider = self::$provider;

            if ($payload->contact) {
                self::whatsappPayloadContact($payload->contact);
                $payload->message->contact = $payload->contact->number;
            }

        }

        if ($payload->delivery) {
            self::$type = "delivery";
            self::whatsappPayloadDelivery($payload->delivery);
        }

        self::$args = $payload;

    }

    /**
     * Método para validar la estructura
     * @param $args Argumento recivido en POST
     * @version        20.05.185.391
     */
    protected static function whatsappPayloadMessage($message)
    {
        \Epys\Wis\Console::log("Epys\Wis\Config\Normalize::whatsappPayloadMessage().");

        // Verifico hora del mensaje
        if (!$message->time)
            \Epys\Wis\Console::error('La hora del mensaje no esta definida [message.time]', \Epys\Wis\Console::ERROR_REQUIRED, __CLASS__, __LINE__);

        if (!is_numeric($message->time))
            \Epys\Wis\Console::error('La hora del mensaje debe ser en formato Unix timestamp [message.time]', \Epys\Wis\Console::ERROR_REQUIRED, __CLASS__, __LINE__);

        // Verifico dirección del mensaje
        if (!in_array($message->direction, ['sent', 'received']))
            \Epys\Wis\Console::error('La dirección del mensaje no está definida [message.direction]', \Epys\Wis\Console::ERROR_REQUIRED, __CLASS__, __LINE__);

        // Si es un envio debe contener cid
        if ($message->direction === 'sent' && !$message->cid)
            \Epys\Wis\Console::error('Debe indicar el ID del cliente [message.cid]', \Epys\Wis\Console::ERROR_REQUIRED, __CLASS__, __LINE__);

        switch ($message->content->type) {
            case "text":
                if (!isset($message->content->text))
                    \Epys\Wis\Console::error("El objeto CONTENT.TEXT no es valido.", \Epys\Wis\Console::ERROR_INPUT_CONTENT_TEXT, __CLASS__, __LINE__);
                break;
            case "image":
                if (!isset($message->content->url))
                    \Epys\Wis\Console::error("El objeto CONTENT.IMAGE no es valido.", \Epys\Wis\Console::ERROR_INPUT_CONTENT_IMAGE, __CLASS__, __LINE__);
                break;
            case "sticker":
                if (!isset($message->content->url))
                    \Epys\Wis\Console::error("El objeto CONTENT.STICKER no es valido.", \Epys\Wis\Console::ERROR_INPUT_CONTENT_STICKER, __CLASS__, __LINE__);
                break;
            case "audio":
                if (!isset($message->content->url))
                    \Epys\Wis\Console::error("El objeto CONTENT.AUDIO no es valido.", \Epys\Wis\Console::ERROR_INPUT_CONTENT_AUDIO, __CLASS__, __LINE__);
                break;
            case "video":
                if (!isset($message->content->url))
                    \Epys\Wis\Console::error("El objeto CONTENT.VIDEO no es valido.", \Epys\Wis\Console::ERROR_INPUT_CONTENT_VIDEO, __CLASS__, __LINE__);
                break;
            case "document":
                if (!isset($message->content->url))
                    \Epys\Wis\Console::error("El objeto CONTENT.DOCUMENT no es valido.", \Epys\Wis\Console::ERROR_INPUT_CONTENT_DOCUMENT, __CLASS__, __LINE__);
                break;
            case "location":
                if (!isset($message->content->longitude) || !isset($message->content->latitude))
                    \Epys\Wis\Console::error("El objeto CONTENT.LOCATION no es valido.", \Epys\Wis\Console::ERROR_INPUT_CONTENT_LOCATION, __CLASS__, __LINE__);
                break;
            default:
                \Epys\Wis\Console::error("El objeto CONTENT no es valido.", \Epys\Wis\Console::ERROR_INPUT_CONTENT, __CLASS__, __LINE__);
                break;
        }
    }

    /**
     * Método para validar la estructura
     * @param $args Argumento recivido en POST
     * @version        20.05.185.391
     */
    protected static function whatsappPayloadDelivery($delivery)
    {
        \Epys\Wis\Console::log("Epys\Wis\Config\Normalize::whatsappPayloadDelivery().");

        // Verifico hora del mensaje
        if (!$delivery->id)
            \Epys\Wis\Console::error('El provider ID no esta definido [delivery.id]', \Epys\Wis\Console::ERROR_REQUIRED, __CLASS__, __LINE__);

        // Verifico hora del mensaje
        if (!$delivery->time)
            \Epys\Wis\Console::error('La hora del mensaje no esta definida [delivery.time]', \Epys\Wis\Console::ERROR_REQUIRED, __CLASS__, __LINE__);

        if (!is_numeric($delivery->time))
            \Epys\Wis\Console::error('La hora del mensaje debe ser en formato Unix timestamp [delivery.time]', \Epys\Wis\Console::ERROR_REQUIRED, __CLASS__, __LINE__);

        if ($delivery->status > 10 || !is_numeric($delivery->status))
            \Epys\Wis\Console::error('El estatus de lectura del mensaje es erroneo [delivery.status]', \Epys\Wis\Console::ERROR_REQUIRED, __CLASS__, __LINE__);

    }

    /**
     * Método para validar la estructura
     * @param $args Argumento recivido en POST
     * @version        20.05.185.391
     */
    protected static function whatsappPayloadContact($contact)
    {
        \Epys\Wis\Console::log("Epys\Wis\Config\Normalize::whatsappPayloadContact().");

        if (!$contact->number)
            \Epys\Wis\Console::error("No existe objeto CONTACT.NUMBER.", \Epys\Wis\Console::ERROR_REQUIRED, __CLASS__, __LINE__);

    }


}

