<?php


namespace Epys\Wis\Google;


class Messaging
{


    /**
     * Token (Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key)
     */
    protected static $token;

    /**
     * Token (Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key)
     */
    protected static $url = "https://fcm.googleapis.com/fcm/send";

    /**
     * Método para enviar data
     * @version 2020-05-06
     */
    public static function withData($device, $data = ["body", "title", "icon", "sound", "type", "tag", "activ"])
    {
        \Epys\Wis\Console::log("Epys\Wis\Google\Messaging::withData().");

        if(!self::$token){
            \Epys\Wis\Console::log("El token de conexión no esta definido. (Firebase Console->Project Settings->CLOUD MESSAGING->Server key)");
            return;
        }

        if ($data["body"] && $data["title"] && $data["icon"]) {
            $json = json_encode([
                "to" => $device,
                "notification" => [
                    "body" => $data["body"],
                    "title" => $data["title"],
                    "icon" => $data["icon"]
                ],
                "data" => $data,
            ]);
        } else {
            $json = json_encode([
                "to" => $device,
                "data" => $data
            ]);
        }


        // Header with content_type api key
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key=' . self::$token
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        $result = curl_exec($ch);
        curl_close($ch);

        \Epys\Wis\Console::log($result);

        return $result;
    }

    /**
     * Método para enviar notificacion
     * @version 2020-05-06
     */
    public static function withNotification($device, $notification = ["body", "title", "icon", "sound", "type", "tag", "activ"])
    {
        \Epys\Wis\Console::log("Epys\Wis\Google\Messaging::withData().");

        if(!self::$token){
            \Epys\Wis\Console::log("El token de conexión no esta definido. (Firebase Console->Project Settings->CLOUD MESSAGING->Server key)");
            return;
        }

        self::validateNotification($notification);

        $json = json_encode([
            "to" => $device,
            "notification" => $notification
        ]);

        // Header with content_type api key
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key=' . self::$token
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        $result = curl_exec($ch);
        curl_close($ch);

        \Epys\Wis\Console::log($result);

        return $result;
    }

    /**
     * Método para asignar token
     * @version 2020-05-06
     */
    public
    static function setToken($tkn)
    {
        \Epys\Wis\Console::log("Epys\Wis\Google\Messaging::setToken().");
        self::$token = $tkn;
    }


    /**
     * Método para asignar token
     * @version 2020-05-06
     */
    protected
    static function validateNotification($notification)
    {
        \Epys\Wis\Console::log("Epys\Wis\Google\Messaging::validateNotification().");

        if (!$notification["body"])
            \Epys\Wis\Console::error("El objeto `body` no esta definido.", \Epys\Wis\Console::ERROR_INPUT, __CLASS__, __LINE__);

        if (!$notification["title"])
            \Epys\Wis\Console::error("El objeto `title` no esta definido.", \Epys\Wis\Console::ERROR_INPUT, __CLASS__, __LINE__);

        if (!$notification["icon"])
            \Epys\Wis\Console::error("El objeto `icon` no esta definido.", \Epys\Wis\Console::ERROR_INPUT, __CLASS__, __LINE__);
    }

}
