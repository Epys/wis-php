<?php

namespace Epys\Wis;

use \DateTime;

class Console
{

    const LOGS = 0;


    const ERROR = 1000;
    const ERROR_REQUIRED = 1100;

    const ERROR_VALIDATION = 1200;
    const ERROR_VALIDATION_DIR = 1210;
    const ERROR_VALIDATION_JSON = 1220;
    const ERROR_VALIDATION_EMPTY = 1250;

    const ERROR_INPUT = 1300;
    const ERROR_DATABASE = 1301;
    const ERROR_INPUT_ID = 1310;
    const ERROR_INPUT_NETWORK = 1310;
    const ERROR_INPUT_TYPE = 1320;
    const ERROR_INPUT_TIME = 1330;
    const ERROR_INPUT_DIRECTION = 1340;
    const ERROR_INPUT_CONTENT = 1350;
    const ERROR_INPUT_CONTENT_TEXT = 1351;
    const ERROR_INPUT_CONTENT_IMAGE = 1352;
    const ERROR_INPUT_CONTENT_STICKER = 1353;
    const ERROR_INPUT_CONTENT_AUDIO = 1354;
    const ERROR_INPUT_CONTENT_VIDEO = 1355;
    const ERROR_INPUT_CONTENT_DOCUMENT = 1356;
    const ERROR_INPUT_CONTENT_LOCATION = 1357;


    // Rutas de escritura
    private static $_path = '/tmp';
    private static $_path_wis = '/wis';
    private static $_path_logs = '/logs';
    private static $_path_input = '/input';
    private static $_path_error = '/error';

    private static $_console = [];

    /**
     * Método que define path de logs
     * @param codigo Código del mensaje que se desea escribir (Clase con códigos de logs o errores)
     * @param msg Mensaje que se desea escribir
     * @version 2020-04-14
     */
    public static function setPath($path = null)
    {

        // Path de escritura
        if (!$path)
            self::error('Debe indicar el Path de escritura.', self::ERROR_REQUIRED);

        $path = rtrim($path, '/');

        if (!is_dir($path))
            self::error('El Path no es valido.', self::ERROR_VALIDATION_DIR);

        // Defino Path de los logs
        self::$_path = $path;

    }

    /**
     * Método que escribe un mensaje en los logs
     * @param codigo Código del mensaje que se desea escribir (Clase con códigos de logs o errores)
     * @param msg Mensaje que se desea escribir
     * @version 2020-04-18
     */
    public static function log($msg = null, $codigo = false)
    {

        if (!$msg)
            return;

        // Formateo mensaje
        $msgs = ((new DateTime())->format('H:i:s:u')) . "\tPID" . getmypid() . "\t\t" . $msg . "\t" . $codigo;

        // Guardo console
        self::$_console['log'][] = [((new \DateTime())->format('H:i:s:u')) => is_array($msg) ? json_encode($msg) : $msg];

        // Guardo registros
        self::_putContents(self::$_path_logs . date("/Y/m"), $msgs, $codigo);

    }

    /**
     * Método que escribe un mensaje en los logs para crons
     * @param codigo Código del mensaje que se desea escribir (Clase con códigos de logs o errores)
     * @param msg Mensaje que se desea escribir
     * @version 2020-04-18
     */
    public static function cron($msg = null, $codigo = false)
    {

        if (!$msg)
            return;

        // Formateo mensaje
        $msgs = ((new DateTime())->format('H:i:s:u')) . "\tPID" . getmypid() . "\t\t" . $msg . "\t" . $codigo;

        // Guardo console
        self::$_console['log'][] = [((new \DateTime())->format('H:i:s:u')) => is_array($msg) ? json_encode($msg) : $msg];

        // Guardo registros
        self::_putContents(self::$_path_logs . date("/Y/m"), $msgs, $codigo);

    }

    /**
     * Método que escribe un mensaje en los logs pero de formato input (json)
     * @param $codigo Código del mensaje que se desea escribir (Clase con códigos de logs o errores)
     * @param $json Lo que recepciona PHP en input
     * @version 2020-04-18
     */
    public static function input($json = null, $codigo = false)
    {

        if (!$json)
            return;

        // Guardo console
        self::$_console['input'][] = $json;

        // Guardo registros
        self::_putContents(self::$_path_input . date("/Y/m"), json_encode($json), $codigo);

    }

    /**
     * Método que escribe un mensaje en los logs pero de formato error
     * @param codigo Código del mensaje que se desea escribir (Clase con códigos de logs o errores)
     * @param msg Mensaje que se desea escribir
     * @version 2020-04-18
     */
    public static function error($msg = null, $codigo = null, $class = __CLASS__, $line = __LINE__)
    {

        if (!$msg)
            return;

        // Formateo mensaje
        $msge = date("Y-m-d H:i:s") . "\tPID" . getmypid() . "\t\t" . $msg . "\t" . $codigo;

        // Guardo console
        self::$_console['error'][] = $msge;

        // Guardo registros
        self::_putContents(self::$_path_error . date("/Y/m"), $msge, $codigo);

        self::log($msg);

        header('Content-Type:application/json');
        die(json_encode(array_filter([
            "success" => false,
            "timestamp" => time(),
            "pid" => getmypid(),
            "class" => $class,
            "line" => $line,
            "code" => $codigo,
            "error" => $msg,
            "logs" => self::$_console['log'],
            "input" => self::$_console['input']
        ], function ($value) {
            return !is_null($value) && $value !== '';
        })));

    }

    /**
     * Método que crea un archivo
     * @param $filepath Código del mensaje que se desea escribir (Clase con códigos de logs o errores)
     * @param $str Mensaje que se desea escribir
     * @param $name Nombre del archivo
     * @version 2020-04-18
     */
    private static function _putContents($filepath, $str, $name = false)
    {

        // Actualizo la ruta de los logs
        $filepath = rtrim(self::$_path . self::$_path_wis . $filepath, '/');
        if (!is_dir($filepath))
            mkdir($filepath, 0777, true);

        // Guardo contenido con nombre especial
        if ($name)
            @file_put_contents($filepath . "/" . $name . ".log", $str . PHP_EOL, FILE_APPEND);

        // Guardo contenido con el día
        @file_put_contents($filepath . "/" . date('d') . ".log", $str . PHP_EOL, FILE_APPEND);

    }


    /**
     * Método que imprime la consola
     * @version 2020-04-18
     */
    public static function print()
    {
        print_r(self::$_console);
    }

    /**
     * Método que imprime la consola
     * @version 2020-04-18
     */
    public static function dump()
    {
        var_dump(self::$_console);
    }

    /**
     * Método que imprime la consola
     * @version 2020-04-18
     */
    public static function debug()
    {
        $obj = array_filter(self::$_console, function ($value) {
            return !is_null($value) && $value !== '';
        });

        header('Content-Type:application/json');
        die(json_encode($obj));
    }

    public static function destruct()
    {

        if (self::$_console['log'])
            self::_putContents(self::$_path_logs . date("/Y/m"), ' ');

        if (self::$_console['error'])
            self::_putContents(self::$_path_error . date("/Y/m"), ' ');

    }


}
