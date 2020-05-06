<?php


// Estas variables se definen en Epys\Wis\Client
// La tabla  [WIT_CONTACT]          \Epys\Wis\Client::$contact
// La tabla  [WIT_TRONCAL]          \Epys\Wis\Client::$trunk
// La tabla  [WIT_CONVERSACION]     \Epys\Wis\Client::$conversation
// La tabla  [WIT_PREGUNTA]         \Epys\Wis\Client::$ask
// La tabla  [WIT_IVR]              \Epys\Wis\Client::$ivr
// La base de datos es \Epys\Wis\Client::$database
// El JSON que envia wis es \Epys\Wis\Client::$args
// Envio mensajes \Epys\Wis\Client::$network->provider(xxx)->contact(yyy)->text(zzz)->send();


// Verifico que datos necesito que esten cargados del modelo Epys\Wis\Client
\Epys\Wis\Client::isLoad(["database", "args", "contact", "trunk", "conversation"]);

// Valido que sea mensaje
if (\Epys\Wis\Client::$args->type != "message" || \Epys\Wis\Client::$args->message->direction != "received")
    \Epys\Wis\Console::error("El tipo de args no es correcto.", \Epys\Wis\Console::ERROR_INPUT_CONTENT_TEXT, __CLASS__, __LINE__);

// Valido que la respuesta sea texto
if (\Epys\Wis\Client::$args->message->content->type != "text") {
    // Envio respuesta
    if (\Epys\Wis\Client::$network->check())
        \Epys\Wis\Client::$network->text("La respuesta debe ser texto.")->send();

    //Error
    \Epys\Wis\Console::error("La respuesta debe ser texto.", \Epys\Wis\Console::ERROR_INPUT_CONTENT_TEXT, __CLASS__, __LINE__);

}

// Valido que la respuesta sea texto
if (!\Epys\Wis\Client::$conversation->RGXP_MATCH)
    \Epys\Wis\Console::error("La pregunta `" . \Epys\Wis\Client::$conversation->DESC_PREGUNTA . "` no tiene un patron asociado.", \Epys\Wis\Console::ERROR_INPUT_CONTENT_TEXT, __CLASS__, __LINE__);


// Asigno respuesta
$texto = trim(\Epys\Wis\Client::$args->message->content->text);

// Valido que la respuesta coincida con el regxp
preg_match(\Epys\Wis\Client::$ask->RGXP_MATCH, $texto, $respuesta);
if (isset($respuesta[0])) {

    \Epys\Wis\Console::log($respuesta);

    // Funcion para validar rut (/libraries/funciones.php)
    if (!validaRut($texto)) {
        // Envio respuesta
        if (\Epys\Wis\Client::$network->check())
            \Epys\Wis\Client::$network->text(\Epys\Wis\Client::$conversation->RGXP_FAIL)->send();

        //Error
        \Epys\Wis\Console::error(\Epys\Wis\Client::$conversation->RGXP_FAIL, \Epys\Wis\Console::ERROR_INPUT_CONTENT_TEXT, __CLASS__, __LINE__);
    }


    $this->db->insert("WI.WIT_RESPUESTA", [
        DESC_RESPUESTA => $texto,
        IDEN_ACTIV => \Epys\Wis\Client::$conversation->IDEN_ACTIV,
        CODI_PREGUNTA => \Epys\Wis\Client::$conversation->CODI_PREGUNTA,
        FECH_PREGUNTA => \Epys\Wis\Client::$conversation->FECH_CONVERSACION
    ]);

    // Envio respuesta
    if (\Epys\Wis\Client::$network->check())
        \Epys\Wis\Client::$network->text(\Epys\Wis\Client::$conversation->RGXP_PASS . " [" . $texto . "]")->send();

    // Limpio conversacion
    \Epys\Wis\Config\Conversation::delContactTrunk();

} else {
    // Envio respuesta
    if (\Epys\Wis\Client::$network->check())
        \Epys\Wis\Client::$network->text(\Epys\Wis\Client::$conversation->RGXP_FAIL)->send();

    \Epys\Wis\Console::error(\Epys\Wis\Client::$conversation->RGXP_FAIL, \Epys\Wis\Console::ERROR_INPUT_CONTENT_TEXT, __CLASS__, __LINE__);
}







