<?php

namespace Epys\Wis;

class Console
{


    private static $console = [];

    /**
     * Método que escribe un mensaje en los logs
     * @param codigo Código del mensaje que se desea escribir (Clase con códigos de logs o errores)
     * @param msg Mensaje que se desea escribir
     * @author Adonías Vásquez (adonias.vasquez[at]epys.cl)
     * @version 2020-04-14
     */
    public static function log($codigo, $msg = null)
    {

        // si el código es un string se copia a msg
        if (is_string($codigo) && !$codigo) {
            $codigo = 0; // código log genérico
        }

        // agregar mensaje a la bitácora
        array_push(self::$console[$codigo], $msg);
    }


}
