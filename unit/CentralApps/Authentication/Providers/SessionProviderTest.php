<?php
namespace CentralApps\Authentication\Providers;

class SessionProviderTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$request = array('test', 'request');
		$this->_userFactory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');
		$this->_userGateway = $this->getMock('\CentralApps\Authentication\UserGateway');
		$this->_provider = new SessionProvider($request, $this->_userFactory, $this->_userGateway);
	}
	
	public function testConstructor()
	{
		$request = array('test', 'request');
		$user_factory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');
		$user_gateway = $this->getMock('\CentralApps\Authentication\UserGateway');
		$provider = new SessionProvider($request, $user_factory, $user_gateway);
		$reflection = new \ReflectionClass("\CentralApps\Authentication\Providers\SessionProvider");
		$request_property = $reflection->getProperty("request");
		$request_property->setAccessible(true);
		$factory_property = $reflection->getProperty("userFactory");
		$factory_property->setAccessible(true);
		$gateway_property = $reflection->getProperty("userGateway");
		$gateway_property->setAccessible(true);
		
		$this->assertEquals($request, $request_property->getValue($provider));
		$this->assertEquals($user_factory, $factory_property->getValue($provider));
		$this->assertEquals($user_gateway, $gateway_property->getValue($provider));
	}
	
	public function testSetSessionName()
	{
		$new_session_name = 'A_NEW_SESSION_NAME';
		$reflection = new \ReflectionClass("\CentralApps\Authentication\Providers\SessionProvider");
		$name_property = $reflection->getProperty("sessionName");
		$name_property->setAccessible(true);
		$this->assertEquals("CA_AUTH_USER_ID", $name_property->getValue($this->_provider));
		$this->_provider->setSessionName($new_session_name);
		$this->assertEquals($new_session_name, $name_property->getValue($this->_provider));
		
	}
	
	/**
	 * @runInSeparateProcess
	 */
	public function testHasAttemptedToLoginWithProvider()
	{
		$reflection = new \ReflectionClass("\CentralApps\Authentication\Providers\SessionProvider");
		$name_property = $reflection->getProperty("sessionName");
		$name_property->setAccessible(true);
		$session_name = $name_property->getValue($this->_provider);
		$this->assertFalse($this->_provider->hasAttemptedToLoginWithProvider());
		$_SESSION[$this->getSessionName()] = 123;
		$this->assertTrue($this->_provider->hasAttemptedToLoginWithProvider());
	}
	
	/**
	 * @runInSeparateProcess
	 */
	public function testProcessLoginAttempt()
	{
		session_start();
		$user_factory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');
		$user_factory->expects($this->once())
					 ->method('getUserByUserId')
					 ->with($this->equalTo(0))
					 ->will($this->throwException(new \Exception()));
		$provider = new SessionProvider(array(), $user_factory, $this->_userGateway);			 
		$this->assertNull($provider->processLoginAttempt());
		$_SESSION['CA_AUTH_USER_ID'] = 345;
		$user_id = 345;
		$user_factory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');
		$user = new \stdClass();
		$user->id = $user_id;
		$user_factory->expects($this->once())
					 ->method('getUserByUserId')
					 ->with($this->equalTo($user_id))
					 ->will($this->returnValue($user));
		$provider = new SessionProvider(array(), $user_factory, $this->_userGateway);			 
		$this->assertEquals($user, $provider->processLoginAttempt());
	}
	
	/**
	 * @runInSeparateProcess
	 */
	public function testPersistLogin()
	{
		$user_id = 667;
		session_start();
		$user_gateway = $this->getMock('\CentralApps\Authentication\UserGateway');
		$user_gateway->expects($this->once())
					 ->method('getUserId')
					 ->will($this->returnValue($user_id));
		$provider = new SessionProvider(array(), $this->_userFactory, $user_gateway);
		$provider->persistLogin();	
		$this->assertEquals($user_id, $_SESSION[$this->getSessionName()]);
	}
	
	private function getSessionName()
	{
		$reflection = new \ReflectionClass("\CentralApps\Authentication\Providers\SessionProvider");
		$name_property = $reflection->getProperty("sessionName");
		$name_property->setAccessible(true);
		return $name_property->getValue($this->_provider);
	}
	
	/**
	 * @runInSeparateProcess
	 */
	public function testLogout()
	{
		session_start();
		$_SESSION['CA_AUTH_USER_ID'] = 345;
		$this->_provider->logout();
		$this->assertEmpty($_SESSION);
	}
	
	public function testUserWantsToBeRemembered()
	{
		$this->assertFalse($this->_provider->userWantsToBeRemembered());
	}
}
