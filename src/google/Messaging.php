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
     * @version        20.11.302.503
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
        $headers = ' --header "Content-Type:application/json"';
        $headers .= ' --header "Authorization:key=' . self::$token . '"';
        $datos = json_encode($json);
        shell_exec('curl -X POST ' . $headers . ' '. self::$url . ' -d ' . $datos . ' 1>/dev/null 2>&1');
        return $json;
    }

    /**
     * Método para enviar notificacion
     * @version        20.11.302.503
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
        $headers = ' --header "Content-Type:application/json"';
        $headers .= ' --header "Authorization:key=' . self::$token . '"';
        $datos = json_encode($json);
        shell_exec('curl -X POST ' . $headers . ' '. self::$url . ' -d ' . $datos . ' 1>/dev/null 2>&1');
        return $json;
    }

    /**
     * Método para asignar token
     * @version        20.11.302.503
     */
    public
    static function setToken($tkn)
    {
        \Epys\Wis\Console::log("Epys\Wis\Google\Messaging::setToken().");
        self::$token = $tkn;
    }


    /**
     * Método para asignar token
     * @version        20.11.302.503
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
