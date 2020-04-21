<?php


namespace Epys\Wis\Network;


class Whatsapp implements NetworkInterface
{

    /**
     * Provider
     */
    const URL = \Epys\Wis\Client::BASE_API . '/whatsapp/send';

    /**
     * Provider
     */
    private static $_provider;

    /**
     * Contact
     */
    private static $_contact;

    /**
     * Transac
     */
    private static $_transac;

    /**
     * Content
     */
    private static $_content;

    /**
     * Create a new Class Whatsapp.
     */
    public function __construct($provider = null, $contact = null, $transac = null)
    {
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Whatsapp::__construct().');

        if ($provider)
            self::provider($provider);

        if ($contact)
            self::contact($contact);

        if ($transac)
            self::transac($transac);

    }

    /**
     * Método para enviar
     * @version 2020-04-20
     */
    public
    function send($provider = null, $contact = null, $transac = null, $content = null)
    {
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Whatsapp::send().');

        if ($provider)
            self::provider($provider);

        if ($contact)
            self::contact($contact);

        if ($transac)
            self::transac($transac);

        if (!self::$_contact)
            \Epys\Wis\Console::error('No esta definido el número de contacto.', \Epys\Wis\Console::ERROR_INPUT_TIME, __CLASS__, __LINE__);

        if (!self::$_provider)
            \Epys\Wis\Console::error('No esta definido el número de proveedor.', \Epys\Wis\Console::ERROR_INPUT_TIME, __CLASS__, __LINE__);

        $json = [
            "id" => self::clientid(),
            "time" => time(),
            "network" => "whatsapp",
            "type" => "message",
            "direction" => "sent",
            "contact" => ["number" => self::$_contact],
            "content" => self::$_content,
            "provider" => ["number" => self::$_provider]
        ];

        //Envio Logs
        \Epys\Wis\Console::log($json);

        // Retorno resultado
        $result = \Epys\Wis\Http\Service::POST(self::URL, $json);

        //Envio Logs
        \Epys\Wis\Console::log($result);

        return $result;

    }

    /**
     * Método para asignar options
     * @version 2020-04-20
     */
    public
    function options($options = [provider => null, contact => null, transac => null])
    {
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Whatsapp::options().');

        if ($options['provider'])
            self::provider($options['provider']);

        if ($options['contact'])
            self::contact($options['contact']);

        if ($options['transac'])
            self::transac($options['transac']);

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
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Whatsapp::provider(' . $provider . ').');

        // Defino proveedor
        self::$_provider = $provider;

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
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Whatsapp::contact(' . $contact . ').');

        // Defino contacto
        self::$_contact = $contact;

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
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Whatsapp::transac(' . $transac . ').');

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
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Whatsapp::text().');
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
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Whatsapp::image().');

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
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Whatsapp::stiker().');

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
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Whatsapp::document().');

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
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Whatsapp::audio().');

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
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Whatsapp::video().');

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
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Whatsapp::location().');

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
        return hexdec(uniqid());
    }

}
