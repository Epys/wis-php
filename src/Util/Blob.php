<?php


namespace Epys\Wis\Util;


class Blob
{

    /**
     * Create __construct
     */
    public function __construct()
    {
        \Epys\Wis\Console::log("Epys\Wis\Util\Blob::__construct().");
    }

    /**
     * Create __call
     */
    public function __call($method, $args)
    {
        \Epys\Wis\Console::log("Epys\Wis\Util\Blob::__call(" . $method . ", " . json_encode($args) . ").");
        // Verifico si existe metodo
        if (isset($this->$method)) {
            $func = $this->$method;
            return call_user_func_array($func, $args);
        }
    }

}
