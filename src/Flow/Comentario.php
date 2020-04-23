<?php


namespace Epys\Wis\Flow;


class Comentario
{

    /**
     * Método para buscar actividades pendientes por contacto
     * @version 2020-04-23
     */
    public static function setComentario()
    {
        \Epys\Wis\Console::log('Epys\Wis\Flow\Comentario::setComentario().');

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(['database', 'args', 'contact']);

        // Valido transac
        if (!\Epys\Wis\Client::$args->transac) {
            \Epys\Wis\Console::error('El objeto transac no es valido.', \Epys\Wis\Console::ERROR_INPUT_CONTENT_TEXT,__CLASS__,__LINE__);
        }

        // Valido que sea un mensaje
        if (\Epys\Wis\Client::$args->type == 'message') {

            \Epys\Wis\Console::log('Verifico typo de documento.');
            switch (\Epys\Wis\Client::$args->content->type) {
                case 'text':
                    self::text();
                    break;
                case 'image':
                    self::image();
                    break;
                case 'sticker':
                    self::sticker();
                    break;
                case 'audio':
                    self::audio();
                    break;
                case 'video':
                    self::video();
                    break;
                case 'document':
                    self::document();
                    break;
                case 'location':
                    self::location();
                    break;
            }

            $comentario = [
                "IDEN_USUARIO" => \Epys\Wis\Client::$contact->IDEN_CONTACTO,
                "IDEN_TRANSAC" => \Epys\Wis\Client::$args->transac,
                "FECH_COMENTARIO" => \Epys\Wis\Client::$args->time,
                "DESC_COMENTARIO" => \Epys\Wis\Client::$args->content->text,
                "NMRO_LATITUDE" => \Epys\Wis\Client::$args->content->latitude,
                "NMRO_LONGITUDE" => \Epys\Wis\Client::$args->content->longitude,
                "FLAG_URL" => \Epys\Wis\Client::$args->content->url,
                "FLAG_MIME" => \Epys\Wis\Client::$args->content->mime,
                "FLAG_TYPE" => \Epys\Wis\Client::$args->type,
                "FLAG_ACKID" => \Epys\Wis\Client::$args->id,
                "THUMB" => \Epys\Wis\Client::$args->content->thumb
            ];

            // Guardo comentario
            \Epys\Wis\Client::$database->insert("SU.SUT_COMENTARIO", $comentario);

            \Epys\Wis\Console::log(([
                sutComentario => array_filter($comentario)
            ]));

            // Envio notificación al usuario
            //\Epys\Wis\Google\Messaging();

        }

    }

    protected
    static function text()
    {
        \Epys\Wis\Console::log('Epys\Wis\Flow\Comentario::text().');
    }

    protected
    static function image()
    {
        \Epys\Wis\Console::log('Epys\Wis\Flow\Comentario::image().');
    }

    protected
    static function sticker()
    {
        \Epys\Wis\Console::log('Epys\Wis\Flow\Comentario::sticker().');
    }

    protected
    static function audio()
    {
        \Epys\Wis\Console::log('Epys\Wis\Flow\Comentario::audio().');
    }

    protected
    static function video()
    {
        \Epys\Wis\Console::log('Epys\Wis\Flow\Comentario::video().');
    }

    protected
    static function document()
    {
        \Epys\Wis\Console::log('Epys\Wis\Flow\Comentario::document().');
    }

    protected
    static function location()
    {
        \Epys\Wis\Console::log('Epys\Wis\Flow\Comentario::location().');
    }

}
