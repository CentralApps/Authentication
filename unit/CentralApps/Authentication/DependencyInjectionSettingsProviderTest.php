<?php
namespace CentralApps\Authentication;

class DependencyInjectionSettingsProviderTest extends \PHPUnit_Framework_TestCase
{
	protected $provider;
	
	public function setup()
	{
		$settings = array('username_field' => 'Value of the username field property',
						  'password_field' => 'Value of the password field property',
						  'remember_password_field' => 'Value of the remember password field property',
						  'remember_password_yes_value' => 'Value of the remember password yes value property',
						  'session_name' => 'Value of the session name property',
						  'cookie_names' => array('Value', 'of', 'the', 'cookie', 'names', 'property'),
						  'user_factory' => $this->getMock('UserFactoryInterface'),
						  'user_gateway' => $this->getMock('UserGateway'),
						  'session_processor' => $this->getMock('Processors\SessionInterface'),
						  'cookie_processor' => $this->getMock('Processors\CookieInterface'));
		$this->provider = new DependencyInjectionSettingsProvider($settings);
	}
	
	public function testConstructor()
	{
		$settings_array = array( 'some' => 'settings', 'stored' => 'in', 'this' => 'object');
		$provider = new DependencyInjectionSettingsProvider($settings_array);
		
		$class = new \ReflectionClass("\CentralApps\Authentication\DependencyInjectionSettingsProvider");
        $property = $class->getProperty('dependency_injection_container');
        $property->setAccessible(true);
        $value = $property->getValue($provider);
		
		$this->assertEquals($settings_array, $value);
	}
	
	public function testGetUsernameField()
	{
		$this->assertEquals('Value of the username field property', $this->provider->getUsernameField());
	}
	
	public function testGetPasswordField()
	{
		$this->assertEquals('Value of the password field property', $this->provider->getPasswordField());
	}

	public function testGetRememberPasswordField()
	{
		$this->assertEquals('Value of the remember password field property', $this->provider->getRememberPasswordField());
	}
	
	public function testGetRememberPasswordYesValue()
	{
		$this->assertEquals('Value of the remember password yes value property', $this->provider->getRememberPasswordYesValue());
	}
	
	public function testGetSessionName()
	{
		$this->assertEquals('Value of the session name property', $this->provider->getSessionName());
	}
	
	public function testGetCookieNames()
	{
		$this->assertEquals(array('Value', 'of', 'the', 'cookie', 'names', 'property'), $this->provider->getCookieNames());
	}
	
	public function testGetUserFactory()
	{
		$this->assertInstanceOf('UserFactoryInterface', $this->provider->getUserFactory());
	}

	public function testGetUserGateway()
	{
		$this->assertInstanceOf('UserGateway', $this->provider->getUserGateway());
	}
	
	public function testGetSessionProcessor()
	{
		$this->assertInstanceOf('Processors\SessionInterface', $this->provider->getSessionProcessor());
	}
	
	public function testGetCookieProcessor()
	{
		$this->assertInstanceOf('Processors\CookieInterface', $this->provider->getCookieProcessor());
	}
	
}
