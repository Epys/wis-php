<?php


namespace Epys\Wis\Network;


interface NetworkInterface
{

    public function send($provider, $contact, $transac, $content);

    public function options($options = ['provider', 'contact', 'transac']);

    public function provider($provider);

    public function contact($contact);

    public function transac($transac);

    public function text($text);

    public function image($file, $caption);

    public function stiker($file, $caption);

    public function document($file, $caption);

    public function audio($file, $caption);

    public function video($file, $caption);

    public function location($latitude, $longitude, $caption);

}

