<?php


namespace Epys\Wis\Flow;

use finfo;

class Comentario
{

    /**
     * Método para buscar actividades pendientes por contacto
     * @version        20.05.185.391
     */
    public static function setComentario()
    {
        \Epys\Wis\Console::log("Epys\Wis\Flow\Comentario::setComentario().");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database", "args", "contact"]);

        // Valido transac
        if (!\Epys\Wis\Client::$args->transac) {
            \Epys\Wis\Console::error("El objeto transac no es valido.", \Epys\Wis\Console::ERROR_INPUT_CONTENT_TEXT, __CLASS__, __LINE__);
        }

        // Valido que sea un mensaje
        if (\Epys\Wis\Client::$args->message) {

            \Epys\Wis\Console::log("Verifico typo de documento.");
            switch (\Epys\Wis\Client::$args->message->content->type) {
                case "text":
                    self::text();
                    break;
                case "image":
                    self::image();
                    break;
                case "sticker":
                    self::sticker();
                    break;
                case "audio":
                    self::audio();
                    break;
                case "video":
                    self::video();
                    break;
                case "document":
                    self::document();
                    break;
                case "location":
                    self::location();
                    break;
            }

            if (\Epys\Wis\Client::$args->message->content->text)
                $text = self::cleanText(\Epys\Wis\Client::$args->message->content->text);

            $comentario = [
                "IDEN_USUARIO" => \Epys\Wis\Client::$contact->IDEN_CONTACTO,
                "IDEN_TRANSAC" => \Epys\Wis\Client::$args->transac,
                "FECH_COMENTARIO" => \Epys\Wis\Client::$args->message->time,
                "DESC_COMENTARIO" => nvl($text, nvl(\Epys\Wis\Client::$args->message->name, nvl(\Epys\Wis\Client::$args->message->caption, md5(time())))),
                "CODI_DIRECCION" => \Epys\Wis\Client::$args->message->direction,
                "NMRO_LATITUDE" => \Epys\Wis\Client::$args->message->content->latitude,
                "NMRO_LONGITUDE" => \Epys\Wis\Client::$args->message->content->longitude,
                "FLAG_URL" => \Epys\Wis\Client::$args->message->content->url,
                "FLAG_MIME" => \Epys\Wis\Client::$args->message->content->mime,
                "FLAG_TYPE" => \Epys\Wis\Client::$args->message->content->type,
                "FLAG_ACKID" => \Epys\Wis\Client::$args->message->id,
                "THUMB" => \Epys\Wis\Client::$args->message->content->thumb
            ];

            // Guardo comentario
            \Epys\Wis\Client::$database->insert("SU.SUT_COMENTARIO", $comentario);

            \Epys\Wis\Console::log(([
                sutComentario => array_filter($comentario)
            ]));

            if (\Epys\Wis\Client::$args->message->direction == "received" && \Epys\Wis\Client::$args->transac > 1) {
                self::messaging(\Epys\Wis\Client::$args->transac);
            }

        }

    }

    /**
     * Método para buscar actividades pendientes por contacto
     * @version        20.05.185.391
     */
    public static function setBot($iden, $text)
    {
        \Epys\Wis\Console::log("Epys\Wis\Flow\Comentario::setBot().");

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(["database"]);


        $comentario = [
            "IDEN_USUARIO" => '000000000001',
            "IDEN_TRANSAC" => $iden,
            "FECH_COMENTARIO" => round(microtime(true) * 1000),
            "DESC_COMENTARIO" => self::cleanText($text),
            "FLAG_TYPE" => "text",
            "CODI_DIRECCION" => "sent"
        ];

        // Guardo comentario
        \Epys\Wis\Client::$database->insert("SU.SUT_COMENTARIO", $comentario);

        \Epys\Wis\Console::log(([
            sutComentario => array_filter($comentario)
        ]));


    }

    protected
    static function cleanText($text)
    {
        return str_replace("\n", "", substr(json_encode(nl2br(trim($text))), 1, -1));
    }

    protected
    static function messaging($transac)
    {
        \Epys\Wis\Console::log("Epys\Wis\Flow\Comentario::messaging().");

        $tran = \Epys\Wis\Client::$database
            ->select("*")
            ->where(["T.IDEN_TRANSAC" => $transac])
            ->join("PA.PAT_USUARIO U", "U.IDEN_USUARIO = T.IDEN_USERTEC")
            ->get("SU.SUT_TRANSAC T")
            ->result()[0];


        if ($tran->FCM) {
            $push = [
                "sound" => "assets/sounds/notifications/intuition.mp3",
                "type" => "message",
                "tag" => "WIS" . $transac
            ];

            return \Epys\Wis\Google\Messaging::withData($tran->FCM, $push);

        }

    }

    protected
    static function text()
    {
        \Epys\Wis\Console::log("Epys\Wis\Flow\Comentario::text().");
    }

    protected
    static function image()
    {
        \Epys\Wis\Console::log("Epys\Wis\Flow\Comentario::image().");

        // Path de archivos
        $water = FCPATH . 'assets/favicon/iso-wis.png';
        $folder = 'whatsapp/' . \Epys\Wis\Client::$args->transac . '/';
        $path = FCPATH . $folder;

        @chmod($path, 0777);

        // Nombre de la imagen
        $nombre = phRut(\Epys\Wis\Client::$contact->IDEN_CONTACTO) . '_' . time();
        $thumb = 'thumb_' . $nombre;
        $url = $folder . $nombre . '.jpg';
        $tmp = sys_get_temp_dir() . "/" . $nombre . '.jpg';

        \Epys\Wis\Client::$args->message->name = $nombre . '.jpg';
        \Epys\Wis\Client::$args->message->content->mime = "image/jpeg";

        //Guardo tamaño original
        $content = file_get_contents(\Epys\Wis\Client::$args->message->content->url);
        _file_put_contents($tmp, $content);

        // Guardar thumb
        $img = new \Epys\Wis\Util\Upload($tmp);

        if ($img->uploaded) {

            // Guardo en tamaño original
            $img->file_new_name_body = $nombre;
            $img->image_watermark = $water;
            $img->image_watermark_position = 'TL';
            $img->image_convert = 'jpg';
            $img->jpeg_quality = 80;
            $img->file_overwrite = true;
            $img->Process($path);

            // Creo previzualización de la imagen en 330x330
            $img->file_new_name_body = $thumb;
            $img->image_watermark = $water;
            $img->image_watermark_position = 'TL';
            $img->file_overwrite = true;
            $img->image_convert = 'jpg';
            $img->jpeg_quality = 10;
            $img->image_resize = true;
            $img->image_ratio_crop = true;
            $img->image_y = 250;
            $img->image_x = 250;
            $img->Process($path);

            if ($img->processed) {
                $img->Clean();

                $im64 = base64_encode(file_get_contents($path . $thumb . '.jpg'));
                \Epys\Wis\Client::$args->message->content->thumb = $im64;

            } else {
                \Epys\Wis\Client::$args->message->content->thumb = base64_encode($content);
            }
        } else {
            \Epys\Wis\Client::$args->message->content->thumb = base64_encode($content);
        }

    }

    protected
    static function sticker()
    {
        \Epys\Wis\Console::log("Epys\Wis\Flow\Comentario::sticker().");
    }

    protected
    static function audio()
    {
        \Epys\Wis\Console::log("Epys\Wis\Flow\Comentario::audio().");
        // Capturo contenido
        $content = file_get_contents(\Epys\Wis\Client::$args->message->content->url);
        $finfo = new \finfo(FILEINFO_MIME);
        $mime = $finfo->buffer($content);
        if (strpos($mime, ";"))
            $mime = explode(';', $mime)[0];
        $ext = explode('/', $mime)[1];

        // Path de archivos
        $folder = 'whatsapp/' . \Epys\Wis\Client::$args->transac . '/';
        $name = md5(time()) . ".ogg";
        $path = $folder . $name;

        _file_put_contents(FCPATH . $path, $content);

        \Epys\Wis\Client::$args->message->content->url = $path;
        \Epys\Wis\Client::$args->message->content->mime = $mime;
        \Epys\Wis\Client::$args->message->content->name = $name;

    }

    protected
    static function video()
    {
        \Epys\Wis\Console::log("Epys\Wis\Flow\Comentario::video().");
        // Capturo contenido
        $content = file_get_contents(\Epys\Wis\Client::$args->message->content->url);
        $finfo = new \finfo(FILEINFO_MIME);
        $mime = $finfo->buffer($content);
        if (strpos($mime, ";"))
            $mime = explode(';', $mime)[0];
        $ext = explode('/', $mime)[1];

        // Path de archivos
        $folder = 'whatsapp/' . \Epys\Wis\Client::$args->transac . '/';
        $name = md5(time()) . "." . $ext;
        $path = $folder . $name;

        _file_put_contents(FCPATH . $path, $content);

        \Epys\Wis\Client::$args->message->content->url = $path;
        \Epys\Wis\Client::$args->message->content->mime = $mime;
        \Epys\Wis\Client::$args->message->content->name = $name;
    }

    protected
    static function document()
    {
        \Epys\Wis\Console::log("Epys\Wis\Flow\Comentario::document().");
        // Capturo contenido
        $content = file_get_contents(\Epys\Wis\Client::$args->message->content->url);
        $finfo = new \finfo(FILEINFO_MIME);
        $mime = $finfo->buffer($content);
        if (strpos($mime, ";"))
            $mime = explode(';', $mime)[0];
        $ext = explode('/', $mime)[1];

        // Path de archivos
        $folder = 'whatsapp/' . \Epys\Wis\Client::$args->transac . '/';
        $name = md5(time()) . "." . $ext;
        $path = $folder . $name;

        _file_put_contents(FCPATH . $path, $content);

        \Epys\Wis\Client::$args->message->content->url = $path;
        \Epys\Wis\Client::$args->message->content->mime = $mime;
        \Epys\Wis\Client::$args->message->content->name = $name;
    }

    protected
    static function location()
    {
        \Epys\Wis\Console::log("Epys\Wis\Flow\Comentario::location().");
    }

}
