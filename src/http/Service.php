<?php


namespace Epys\Wis\Http;


class Service
{

    /**
     * Método para enviar mensaje
     * @version 2020-04-20
     */
    public
    static function POST($url, $obj)
    {

        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Service::POST(' . $url . ').');

        if (!$url)
            \Epys\Wis\Console::error('No esta definido el url de envio.', \Epys\Wis\Console::ERROR_INPUT_TIME, __CLASS__, __LINE__);

        $fields = json_encode($obj);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($fields)]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, FALSE);

        return $result;

    }

}
