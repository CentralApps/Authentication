<?php
namespace CentralApps\Authentication\Processors\Standard;

class SessionTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$session_name = 'some session name';
		$object = new \CentralApps\Authentication\Processors\Standard\Session($session_name);
		$class = new \ReflectionClass("\CentralApps\Authentication\Processors\Standard\Session");
        $property = $class->getProperty('sessionName');
        $property->setAccessible(true);
        $this->assertEquals($session_name, $property->getValue($object), "Session name was not set by session processor constructor");
	}
}
