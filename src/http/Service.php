<?php


namespace Epys\Wis\Http;


class Service
{

    /**
     * Método para enviar mensaje
     * @version        20.05.185.391
     */
    public
    static function POST($url, $obj)
    {
        \Epys\Wis\Console::log("Epys\Wis\Http\Service::POST(" . $url . ").");

        if (!$url)
            \Epys\Wis\Console::error("No esta definido el url de envio.", \Epys\Wis\Console::ERROR_INPUT_TIME, __CLASS__, __LINE__);

        $fields = json_encode($obj);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: " . \Epys\Wis\Client::$token,
            "Content-Length: " . strlen($fields)
        ]);        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);

        $response = curl_exec($ch);

        if (curl_errno($ch))
            $response = "[E]" . curl_error($ch);

        curl_close($ch);

        $result = json_decode($response, FALSE);


        \Epys\Wis\Console::output($fields);
        \Epys\Wis\Console::output($response);

        return $result;

    }

}
