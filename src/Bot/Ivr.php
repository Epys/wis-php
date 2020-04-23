<?php


namespace Epys\Wis\Bot;


class Ivr
{

    /**
     * Create a new Bot.
     */
    public static function Init()
    {

        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Ivr::Init().');

        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(['database', 'args']);

        // Valido
        if (\Epys\Wis\Client::$conversation->CODI_IVR) {
            self::Respuesta();
        } else {
            self::Pregunta();
        }


    }


    /**
     * Pregunta IVR.
     */
    protected static function Pregunta($ivr = false)
    {
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Ivr::Pregunta(' . $ivr . ').');

        // Si no envio IVR, per defecto es el de la troncal
        if (!$ivr)
            $ivr = \Epys\Wis\Client::$trunk->IDEN_IVR;


        // Verifico que no exista actividad temporal
        if (\Epys\Wis\Client::$activ->IDEN_ACTIV) {
            \Epys\Wis\Console::error('El contacto +' . \Epys\Wis\Client::$args->contact->number . ' ya posee una actividad pendiente.', \Epys\Wis\Console::ERROR_INPUT, __CLASS__, __LINE__);
        }

        // Verifico que la troncal tenga un IVR
        if (!$ivr) {
            \Epys\Wis\Console::error('No podemos identificar el IVR asociado.', \Epys\Wis\Console::ERROR_INPUT, __CLASS__, __LINE__);
        }

        // Genero IVR
        $mensaje = self::_generar($ivr);

        // Si no existe ivr elimino conversación y retorno
        if (!$mensaje) {
            \Epys\Wis\Config\Conversation::delContactTrunk();
            \Epys\Wis\Console::log('El IVR ' . $ivr . ' no tiene datos.');
            return;
        }

        \Epys\Wis\Config\Conversation::setContactTrunk(["IDEN_IVR" => $ivr]);

        // Envio Mensaje por Wsap
        \Epys\Wis\Client::$network->text($mensaje)->send();
    }

    /**
     * Respuesta IVR.
     */
    protected static function Respuesta()
    {
        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Ivr::Respuesta().');

        // Verifico que la troncal tenga un IVR
        if (!\Epys\Wis\Client::$conversation->IDEN_IVR) {
            \Epys\Wis\Console::error('La troncal ' . \Epys\Wis\Client::$args->provider->number . ' no tiene un IVR asociado.', \Epys\Wis\Console::ERROR_INPUT, __CLASS__, __LINE__);
        }

        // Verifico que el IVR tenga REGXP
        if (!\Epys\Wis\Client::$conversation->RGXP_MATCH) {
            \Epys\Wis\Console::error('El IVR ' . \Epys\Wis\Client::$conversation->DESC_IVR . '´ no tiene un patrón asociado.', \Epys\Wis\Console::ERROR_INPUT, __CLASS__, __LINE__);
        }

        // Verifico que el IVR tenga REGXP
        if (\Epys\Wis\Client::$args->content->type !== 'text') {
            \Epys\Wis\Console::error('La respuesta debe ser texto [' . \Epys\Wis\Client::$args->content->type . '].', \Epys\Wis\Console::ERROR_INPUT, __CLASS__, __LINE__);
        }


        //Valido que cumpla con regxp
        \Epys\Wis\Console::log(\Epys\Wis\Client::$conversation->RGXP_MATCH . ' <-> ' . \Epys\Wis\Client::$args->content->text);
        preg_match(\Epys\Wis\Client::$conversation->RGXP_MATCH, \Epys\Wis\Client::$args->content->text, $respuesta);
        if (isset($respuesta[0])) {

            //Si la respuesta es 0
            if ($respuesta[0] == "0") {
                //Vuelvo a mostrar menu IVR
                self::_volver(\Epys\Wis\Client::$conversation->IDEN_IVR);
            } else {

                // Busco si existe el IVR en base a su código
                $ivr = self::codi(\Epys\Wis\Client::$conversation->CODI_IVR . $respuesta[0]);
                \Epys\Wis\Console::log(\Epys\Wis\Client::$conversation->CODI_IVR . $respuesta[0]);
                if ($ivr) {

                    // Guardo datos del IVR para acciones
                    \Epys\Wis\Client::setIvr($ivr);

                    // Verifico el horario de atencion
                    \Epys\Wis\Bot\Schedule::availableIden($ivr->IDEN_HORARIO);

                    // Verifico si tiene Sub IVR
                    self::Pregunta($ivr->IDEN_IVR);

                    // Valido que el IVR tenga una acción o pregunta
                    if ($ivr->CODI_ACCION) {

                        $Action = new \Epys\Wis\Bot\Action();
                        eval('$Action->run = function () { ' . $Action::blob($ivr->CODI_ACCION) . '};');
                        $Action->run();
                    }

                    // Pausa por 3 segundos
                    sleep(3);



                } else {
                    // Envio Mensaje por Wsap
                    \Epys\Wis\Client::$network->text("No existe opción " . $respuesta[0] . " en este menú.")->send();
                }
            }

        } else {
            // Envio Mensaje por Wsap
            \Epys\Wis\Client::$network->text(\Epys\Wis\Client::$conversation->RGXP_FAIL)->send();
        }

    }


    private
    static function _generar($parent = false)
    {

        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Ivr::_generar(' . $parent . ').');

        if (!$parent)
            return;

        // Busco menus asociados
        $menus = \Epys\Wis\Client::$database->where(["IDEN_PARENT" => $parent, "ACTIVO" => 1])->order_by("IDEN_IVR ASC")->get("WI.WIT_IVR")->result();

        $msj = null;

        if ($menus)
            foreach ($menus as $menu)
                $msj .= $menu->DESC_IVR . PHP_EOL;


        //Envio Logs
        \Epys\Wis\Console::log($msj);

        return $msj;

    }

    private
    static function _volver($iden)
    {

        //Envio Logs
        \Epys\Wis\Console::log('Inicio function Ivr::_volver().');

        // Limpio conversacion
        \Epys\Wis\Config\Conversation::delContactTrunk();

        // Menu IVR
        $parent = self::iden($iden);

        // Ejecuto IVR
        if ($parent->IDEN_PARENT == '0') {
            self::Pregunta($iden);
        } else {
            self::Pregunta($parent->IDEN_PARENT);
        }


    }

    public
    static function iden($iden)
    {
        // Verifico que esten cargados los datos
        \Epys\Wis\Client::isLoad(['database']);

        return \Epys\Wis\Client::$database->where(["I.IDEN_IVR" => $iden, "I.ACTIVO" => 1])
            ->get("WI.WIT_IVR I")->result()[0];
    }

    public
static function codi($codi)
{
    // Verifico que esten cargados los datos
    \Epys\Wis\Client::isLoad(['database']);

    return \Epys\Wis\Client::$database->where(["I.CODI_IVR" => $codi, "I.ACTIVO" => 1])
        ->get("WI.WIT_IVR I")->result()[0];
}

}
