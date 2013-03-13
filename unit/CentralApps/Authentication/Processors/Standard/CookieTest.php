<?php
namespace CentralApps\Authentication\Processors\Standard;

class CookieTest extends \PHPUnit_Framework_TestCase
{
	
	protected $cookieNames = array('cookie_id', 'cookie_hash');
	
	public function setUp()
	{
		$this->object = new Cookie($this->cookieNames);
	}
	
	public function tearDown()
	{
		foreach($this->cookieNames as $cookie_name) {
            setcookie($cookie_name,"",time()-3600);
        }
		$_COOKIE = array();
	}
	
	/**
	 * @runInSeparateProcess
	 */
	public function testConstructor()
	{
		$cookie_names = array('some', 'cookie', 'names');
		$object = new \CentralApps\Authentication\Processors\Standard\Cookie($cookie_names);
		$class = new \ReflectionClass("\CentralApps\Authentication\Processors\Standard\Cookie");
        $property = $class->getProperty('cookieNames');
        $property->setAccessible(true);
        $this->assertEquals($cookie_names, $property->getValue($object), "Cookie names were not set by cookie processor constructor");
	}
	
	/**
	 * @runInSeparateProcess
	 */
	public function testCheckForAuthenticationCookie()
	{
		$this->assertFalse($this->object->checkForAuthenticationCookie());
		foreach($this->cookieNames as $cookie_name) {
            $_COOKIE[$cookie_name] = 'some fake value';
        }
		$this->assertTrue($this->object->checkForAuthenticationCookie());
	}
	
	/**
	 * @runInSeparateProcess
	 */
	public function testGetCookieValues()
	{
		$cookie_values = array();
		foreach($this->cookieNames as $cookieName) {
			$cookie_values[ $cookieName ] = 'value_' . $cookieName;
		}
		$_COOKIE = $cookie_values;
		$this->assertEquals($cookie_values, $this->object->getCookieValues());
	}

}
