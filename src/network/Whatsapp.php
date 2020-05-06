<?php


namespace Epys\Wis\Network;


use DateTime;

class Whatsapp implements NetworkInterface
{

    /**
     * Provider
     */
    const URL = \Epys\Wis\Client::BASE_API . "/send";

    /**
     * Provider
     */
    private static $provider = false;

    /**
     * Contact
     */
    private static $contact = false;

    /**
     * Transac
     */
    private static $_transac = false;

    /**
     * Content
     */
    private static $_content;

    /**
     * Create a new Class Whatsapp.
     */
    public function __construct($provider = null, $contact = null, $transac = null)
    {
        \Epys\Wis\Console::log("Epys\Wis\Network\Whatsapp::__construct().");

        if ($provider)
            self::provider($provider);

        if ($contact)
            self::contact($contact);

        if ($transac)
            self::transac($transac);

    }

    /**
     * Método para chequear si esta proveedor y contacto
     * @version 2020-04-22
     */
    public
    function check(): bool
    {
        \Epys\Wis\Console::log("Epys\Wis\Network\Whatsapp::check(" . ((self::$provider && self::$contact) ? true : false) . ").");

        return (self::$provider && self::$contact) ? true : false;
    }

    /**
     * Método para enviar
     * @version 2020-04-20
     */
    public
    function send($provider = null, $contact = null, $transac = null, $content = null)
    {
        \Epys\Wis\Console::log("Epys\Wis\Network\Whatsapp::send().");

        if ($provider)
            self::provider($provider);

        if ($contact)
            self::contact($contact);

        if ($transac)
            self::transac($transac);

        if (!self::$contact)
            \Epys\Wis\Console::error("No esta definido el número de contacto.", \Epys\Wis\Console::ERROR_INPUT_TIME, __CLASS__, __LINE__);

        if (!self::$provider)
            \Epys\Wis\Console::error("No esta definido el número de proveedor.", \Epys\Wis\Console::ERROR_INPUT_TIME, __CLASS__, __LINE__);


        $json = [
            "whatsapp" => [
                self::$provider => [
                    "contact" => ["number" => self::$contact],
                    "message" => [
                        "cid" => self::clientid(),
                        "direction" => "sent",
                        "transac" => self::$_transac,
                        "time" => round(microtime(true) * 1000),
                        "content" => self::$_content
                    ]
                ]
            ]
        ];

        // Retorno resultado
        $result = \Epys\Wis\Http\Service::POST(self::URL, $json);

        return $result;

    }

    /**
     * Método para asignar options
     * @version 2020-04-20
     */
    public
    function options($options = ["provider", "contact", "transac"])
    {
        \Epys\Wis\Console::log("Epys\Wis\Network\Whatsapp::options().");

        if ($options["provider"])
            self::provider($options["provider"]);

        if ($options["contact"])
            self::contact($options["contact"]);

        if ($options["transac"])
            self::transac($options["transac"]);

        // Retorno Clase
        return $this;
    }

    /**
     * Método para asignar model
     * @version 2020-04-20
     */
    public
    function provider($provider)
    {
        \Epys\Wis\Console::log("Epys\Wis\Network\Whatsapp::provider(" . $provider . ").");

        // Valido que sea numerico
        if (!is_numeric($provider))
            return;

        // Defino proveedor
        self::$provider = $provider;

        // Retorno Clase
        return $this;
    }

    /**
     * Método para asignar model
     * @version 2020-04-20
     */
    public
    function contact($contact)
    {
        \Epys\Wis\Console::log("Epys\Wis\Network\Whatsapp::contact(" . $contact . ").");

        // Valido que sea numerico
        if (!is_numeric($contact))
            return;

        // Defino contacto
        self::$contact = $contact;

        // Retorno Clase
        return $this;
    }

    /**
     * Método para asignar model
     * @version 2020-04-20
     */
    public
    function transac($transac)
    {
        \Epys\Wis\Console::log("Epys\Wis\Network\Whatsapp::transac(" . $transac . ").");

        // Defino transac
        self::$_transac = $transac;

        // Retorno Clase
        return $this;
    }

    /**
     * Método para enviar text
     * @version 2020-04-20
     */
    public
    function text($text)
    {
        \Epys\Wis\Console::log("Epys\Wis\Network\Whatsapp::text().");

        self::$_content = \Epys\Wis\Network\Whatsapp\Text::Normalize($text);

        // Retorno Clase
        return $this;
    }

    /**
     * Método para enviar imagen
     * @version 2020-04-20
     */
    public
    function image($file, $caption)
    {
        \Epys\Wis\Console::log("Epys\Wis\Network\Whatsapp::image().");

        self::$_content = \Epys\Wis\Network\Whatsapp\Image::Normalize($file, $caption);

        // Retorno Clase
        return $this;
    }

    /**
     * Método para enviar stiker
     * @version 2020-04-20
     */
    public
    function stiker($file, $caption)
    {
        \Epys\Wis\Console::log("Epys\Wis\Network\Whatsapp::stiker().");

        self::$_content = \Epys\Wis\Network\Whatsapp\Stiker::Normalize($file, $caption);

        // Retorno Clase
        return $this;
    }

    /**
     * Método para enviar documento
     * @version 2020-04-20
     */
    public
    function document($file, $caption)
    {
        \Epys\Wis\Console::log("Epys\Wis\Network\Whatsapp::document().");

        self::$_content = \Epys\Wis\Network\Whatsapp\Document::Normalize($file, $caption);

        // Retorno Clase
        return $this;
    }

    /**
     * Método para enviar audio
     * @version 2020-04-20
     */
    public
    function audio($file, $caption)
    {
        \Epys\Wis\Console::log("Epys\Wis\Network\Whatsapp::audio().");

        self::$_content = \Epys\Wis\Network\Whatsapp\Audio::Normalize($file, $caption);

        // Retorno Clase
        return $this;
    }

    /**
     * Método para enviar video
     * @version 2020-04-20
     */
    public
    function video($file, $caption)
    {
        \Epys\Wis\Console::log("Epys\Wis\Network\Whatsapp::video().");

        self::$_content = \Epys\Wis\Network\Whatsapp\Video::Normalize($file, $caption);

        // Retorno Clase
        return $this;
    }

    /**
     * Método para enviar localizador
     * @version 2020-04-20
     */
    public
    function location($latitude, $longitude, $caption)
    {
        \Epys\Wis\Console::log("Epys\Wis\Network\Whatsapp::location().");

        self::$_content = \Epys\Wis\Network\Whatsapp\Location::Normalize($latitude, $longitude, $caption);

        // Retorno Clase
        return $this;
    }

    /**
     * Método para generar ID unico
     * @version 2020-04-20
     */
    protected
    static function clientid()
    {
        $id = (new DateTime())->format("ynjGisu");
        \Epys\Wis\Console::log("Epys\Wis\Network\Whatsapp::clientid(" . $id . ").");

        return $id;
    }

}
