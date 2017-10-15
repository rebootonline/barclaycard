<?php 

class BarclaycardTest extends PHPUnit_Framework_TestCase{

    public function testIsThereAnySyntaxError(){
    	$var = new Reboot\Barclaycard("123","username", "password", "api_password", "www.abc.com");
    	$this->assertTrue(is_object($var));
    	unset($var);
    }
  
  
  
}