<?php
namespace CentralApps\Authentication\Processors\Standard;

class SessionTest extends \PHPUnit_Framework_TestCase
{
	protected $sessionName = 'session_name_test';
	public function setUp()
	{
		session_start();
		$this->object = new \CentralApps\Authentication\Processors\Standard\Session($this->sessionName);
	}
	
	public function tearDown()
	{
		session_destroy();
	}
	
	public function testConstructor()
	{
		$session_name = 'some session name';
		$object = new \CentralApps\Authentication\Processors\Standard\Session($session_name);
		$class = new \ReflectionClass("\CentralApps\Authentication\Processors\Standard\Session");
        $property = $class->getProperty('sessionName');
        $property->setAccessible(true);
        $this->assertEquals($session_name, $property->getValue($object), "Session name was not set by session processor constructor");
	}
	
	public function testCheckForAuthenticationSession()
	{
		$this->assertFalse($this->object->checkForAuthenticationSession());
		$_SESSION[ $this->sessionName ] = 123;
		$this->assertTrue($this->object->checkForAuthenticationSession());
	}
	
	/**
	 * @covers CentralApps\Authentication\Processors\Standard\Session::setSessionValue
	 */
	public function testSetSessionValue()
	{
		$test_value =  5678;
		$this->object->setSessionValue($test_value);
		$this->assertEquals($test_value, $this->object->getUserId());
	}
	
	/**
	 * @covers CentralApps\Authentication\Processors\Standard\Session::getUserId
	 */
	public function testGetUserId()
	{
		$test_value =  91011;
		$this->object->setSessionValue($test_value);
		$this->assertEquals($test_value, $this->object->getUserId());
	}
	
	/**
	 * @covers CentralApps\Authentication\Processors\Standard\Session::logout
	 */
	public function testSessionLogout()
	{
		$test_value =  9999;
		$this->object->setSessionValue($test_value);
		$this->object->logout();
		$this->assertFalse($this->object->checkForAuthenticationSession());
	}
}
