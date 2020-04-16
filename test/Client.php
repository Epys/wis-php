<?php

include __DIR__ . "/../vendor/autoload.php";


// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

Class Client extends PHPUnit_Framework_TestCase {

    public function wis(){
        $Client = new \Epys\Wis\Client();
        $this->assertEquals(true, true);
    }
}
