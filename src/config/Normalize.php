<?php


namespace Epys\Wis\Config;


class Normalize
{

    /**
     * Método para capturar input de PHP
     * @author Adonías Vásquez (adonias.vasquez[at]epys.cl)
     * @version 2020-04-14
     */
    public static function Input()
    {
        // Capturo datos
        $args = json_decode(file_get_contents('php://input'), FALSE);

        // Verifico que los datos recepcionados vengan en formato JSON
        if (json_last_error() == JSON_ERROR_NONE) {
            \Epys\Wis\Console::error('El formato no es JSON', \Epys\Wis\Console::ERROR_VALIDATION_JSON);
            throw new Exception('El formato de entrada no es JSON', \Epys\Wis\Console::ERROR_VALIDATION_JSON);
        }

        // Guardo datos recepcionados en logs
        \Epys\Wis\Console::input($args);

        return $args;
    }

}
