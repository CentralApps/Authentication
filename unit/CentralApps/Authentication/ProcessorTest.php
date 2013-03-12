<?php
namespace CentralApps\Authentication;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->_processor = new Processor($this->getMock('\CentralApps\Authentication\SettingsProviderInterface'), array());
	}
	
	public function testConstructor()
	{
		$post_data = array('some', 'data');
		$processor = new Processor($this->getMock('\CentralApps\Authentication\SettingsProviderInterface'), $post_data);
		$class = new \ReflectionClass("\CentralApps\Authentication\Processor");
        $property = $class->getProperty('postData');
        $property->setAccessible(true);
        $value = $property->getValue($processor);
		$this->assertEquals($post_data, $value);
	}
	
	public function _testCheckForAuthentication()
	{
		//$this->_processor->checkForAuthentication(true);
		//$this->assertTrue(true);
	}
	
	/**
	 * @covers \CentralApps\Authentication\Processor::checkForAuthentication
	 * @covers \CentralApps\Authentication\Processor::authenticateFromUserId
	 */
	public function testLoggingInWithSession()
	{
		$session_processor = $this->getMock('\CentralApps\Authentication\Processors\SessionInterface');
		$session_processor->expects($this->once())
						  ->method('checkForAuthenticationSession')
						  ->will($this->returnValue(true));
		$session_processor->expects($this->once())
						  ->method('getUserId')
						  ->will($this->returnValue(1));
		$settings_provider = $this->getMock('\CentralApps\Authentication\SettingsProviderInterface');
		$settings_provider->expects($this->once())
						  ->method('getSessionProcessor')
						  ->will($this->returnValue($session_processor));
						  
		$user = new \stdClass;
		$user->userId = 1;
		
		$user_factory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');
		$user_factory->expects($this->once())
					 ->method('getUserByUserId')
					 ->with($this->equalTo(1))
					 ->will($this->returnValue($user));
					 
		$settings_provider->expects($this->once())
						  ->method('getUserFactory')
						  ->will($this->returnValue($user_factory));
		$user_gateway = $this->getMock('UserGatewayInterface');
		$settings_provider->expects($this->once())
						  ->method('getUserGateway')
						  ->will($this->returnValue($user_gateway));
		$processor = new Processor($settings_provider);
		$processor->checkForAuthentication(false);			 
		$this->assertTrue($processor->hasAttemptedToLogin(), "It doesn't look like we tried to login the user");	
		$this->assertEquals($user, $processor->getUser());		 
		
	}
	
	public function testHasAttemptedToLogin()
	{
		$class = new \ReflectionClass("\CentralApps\Authentication\Processor");
        $property = $class->getProperty('loginAttempted');
        $property->setAccessible(true);
        $property->setValue($this->_processor, true);
		$this->assertTrue($this->_processor->hasAttemptedToLogin());
		$property->setValue($this->_processor, false);
		$this->assertFalse($this->_processor->hasAttemptedToLogin());	
	}
	
	/**
	 * @covers CentralApps\Authentication\Processor::checkForAuthentication
	 * @covers CentralApps\Authentication\Processor::authenticateFromCookieValues
	 */
	public function testCheckForAuthenticationWithCookies()
	{
		$cookie_values = array('some' => 'cookie', 'values' => 'here');
		$cookie_processor = $this->getMock('\CentralApps\Authentication\Processors\CookieInterface');
		$cookie_processor->expects($this->once())
						  ->method('checkForAuthenticationCookie')
						  ->will($this->returnValue(true));
		$cookie_processor->expects($this->once())
						 ->method('getCookieValues')
						 ->will($this->returnValue($cookie_values));
		$factory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');
		$user = new \stdClass();
		$user->userId = 1;
		$factory->expects($this->once())
			    ->method('getByCookieValues')
				->with($this->equalTo($cookie_values))
				->will($this->returnValue($user));
		$settings = $this->getMock('\CentralApps\Authentication\SettingsProviderInterface');
		$settings->expects($this->once())
				 ->method('getCookieProcessor')
				 ->will($this->returnValue($cookie_processor));
		$user_gateway = $this->getMock('UserGatewayInterface');
		$settings->expects($this->once())
						  ->method('getUserGateway')
						  ->will($this->returnValue($user_gateway));
		$settings->expects($this->once())
				 ->method('getUserFactory')
				 ->will($this->returnValue($factory));
		$processor = new \CentralApps\Authentication\Processor($settings, array());
		$processor->checkForAuthentication();
		$this->assertTrue($processor->hasAttemptedToLogin());
		$this->assertEquals($user, $processor->getUser());
	}
	
	public function testLoginWithUsernameAndPassword()
	{
		$user_field = 'username';
		$user_val = 'michael';
		$pass_field = 'password';
		$pass_val = 'test_password';
		$post_data = array($user_field => $user_val, $pass_field => $pass_val);
		$settings = $this->getMock('\CentralApps\Authentication\SettingsProviderInterface');
		$settings->expects($this->once())
				 ->method('getUsernameField')
				 ->will($this->returnValue($user_field));
		$settings->expects($this->once())
				 ->method('getPasswordField')
				 ->will($this->returnValue($pass_field));
				 
				 
		$user = new \stdClass;
		$user->userId = 1;
		$user_factory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');		 
		$user_factory->expects($this->once())
					 ->method('getUserFromUsernameAndPassword')
					 ->with($this->equalTo($user_val), $this->equalTo($pass_val))
					 ->will($this->returnValue($user));
		
		
		$settings->expects($this->once())
				 ->method('getUserFactory')
				 ->will($this->returnValue($user_factory));
		
		$session = $this->getMock('\CentralApps\Authentication\Processors\SessionInterface');
		$session->expects($this->once())
				->method('setSessionValue')
				->with($this->equalTo(1));
		
		$settings->expects($this->once())
				 ->method('getSessionProcessor')
				 ->will($this->returnValue($session));
				 
		$user_gateway = $this->getMock('\CentralApps\Authentication\UserGateway');
		$user_gateway->expects($this->once())	
					 ->method('getUserId')
					 ->will($this->returnValue(1));
		
		$settings->expects($this->once())
				 ->method('getUserGateway')
				 ->will($this->returnValue($user_gateway));	 
				 
		$cookie_processor = $this->getMock('\CentralApps\Authentication\Processors\CookieInterface');
		$cookie_processor->expects($this->never())
						 ->method('rememberUser');
		$settings->expects($this->any())
				 ->method('getCookieProcessor')
				 ->will($this->returnValue($cookie_processor));		
				 
		$processor = new Processor($settings, $post_data);
		$processor->checkForAuthentication();
		$this->assertTrue($processor->hasAttemptedToLogin(), "It doesn't look like we tried to login the user");	
		$this->assertEquals($user, $processor->getUser());
	}

	public function testAuthWithUserIdFailsGracefullyOnException()
	{
		$fake_user_id = 5;
		// test throwing of the exception
		$user_factory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');
		$user_factory->expects($this->once())
					 ->method('getUserByUserId')
					 ->with($this->equalTo($fake_user_id))
					 ->will($this->throwException(new \Exception()));
		$settings = $this->getMock('\CentralApps\Authentication\SettingsProviderInterface');
		$settings->expects($this->once())
				 ->method('getUserFactory')
				 ->will($this->returnValue($user_factory));	
				 
		$reflection_class = new \ReflectionClass("\CentralApps\Authentication\Processor");
		$method = $reflection_class->getMethod("authenticateFromUserId");
		$method->setAccessible(true);

		$processor = new \CentralApps\Authentication\Processor($settings);

		$this->assertNull($method->invoke($processor, $fake_user_id));
		
	}
	
	public function testAuthWithCookieValuesFailsGracefullyOnException()
	{
		$cookie_values = array('some' => 'cookie', 'values' => 'here');
		// test throwing of the exception
		$user_factory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');
		$user_factory->expects($this->once())
					 ->method('getByCookieValues')
					 ->with($this->equalTo($cookie_values))
					 ->will($this->throwException(new \Exception()));
		$settings = $this->getMock('\CentralApps\Authentication\SettingsProviderInterface');
		$settings->expects($this->once())
				 ->method('getUserFactory')
				 ->will($this->returnValue($user_factory));	
				 
		$reflection_class = new \ReflectionClass("\CentralApps\Authentication\Processor");
		$method = $reflection_class->getMethod("authenticateFromCookieValues");
		$method->setAccessible(true);

		$processor = new \CentralApps\Authentication\Processor($settings);

		$this->assertNull($method->invoke($processor, $cookie_values));
		
	}

	public function testAuthWithUserAndPasswordFailsGracefullyOnException()
	{
		$fake_user = 'fake-user';
		$fake_pass = 'fake-pass';
		// test throwing of the exception
		$user_factory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');
		$user_factory->expects($this->once())
					 ->method('getUserFromUsernameAndPassword')
					 ->with($this->equalTo($fake_user), $this->equalTo($fake_pass))
					 ->will($this->throwException(new \Exception()));
		$settings = $this->getMock('\CentralApps\Authentication\SettingsProviderInterface');
		$settings->expects($this->once())
				 ->method('getUserFactory')
				 ->will($this->returnValue($user_factory));	
				 
		$reflection_class = new \ReflectionClass("\CentralApps\Authentication\Processor");
		$method = $reflection_class->getMethod("authenticateFromUsernameAndPassword");
		$method->setAccessible(true);

		$processor = new \CentralApps\Authentication\Processor($settings);

		$this->assertNull($method->invoke($processor, $fake_user, $fake_pass));
		
	}

	/**
	 * @covers CentralApps\Authentication\Processor::checkForAuthentication
	 * @covers CentralApps\Authentication\Processor::rememberUser
	 */
	public function testRememberingPassword()
	{
		$user_field = 'username';
		$user_val = 'michael';
		$pass_field = 'password';
		$pass_val = 'test_password';
		$remember_pass_field = 'remember_me';
		$remember_pass_value = 1;
		$post_data = array($user_field => $user_val, $pass_field => $pass_val, $remember_pass_field => $remember_pass_value);
		$cookie_values = array('cookie' => 'values', 'are' => 'expected');
		
		$user_gateway = $this->getMock('\CentralApps\Authentication\UserGateway');
		$user_gateway->expects($this->once())
					 ->method('getCookieValues')
					 ->will($this->returnValue($cookie_values));
		
		
		$settings = $this->getMock('\CentralApps\Authentication\SettingsProviderInterface');
		$settings->expects($this->once())
				 ->method('getUserGateway')
				 ->will($this->returnValue($user_gateway));
				 
		$user_factory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');	
		$user = new \stdClass;
		$user->userId = 1;
		$user_factory->expects($this->once())
					 ->method('getUserFromUsernameAndPassword')
					 ->with($this->equalTo($user_val), $this->equalTo($pass_val))
					 ->will($this->returnValue($user));	 
		$settings->expects($this->once())
				 ->method('getUserFactory')
				 ->will($this->returnValue($user_factory));		 
				 
		$session = $this->getMock('\CentralApps\Authentication\Processors\SessionInterface');
		$settings->expects($this->any())
				 ->method('getSessionProcessor')
				 ->will($this->returnValue($session));
		
		$settings->expects($this->once())
				 ->method('getUsernameField')
				 ->will($this->returnValue($user_field));
		$settings->expects($this->once())
				 ->method('getPasswordField')
				 ->will($this->returnValue($pass_field));
		$settings->expects($this->once())
				 ->method('getRememberPasswordField')
				 ->will($this->returnValue($remember_pass_field));
		$settings->expects($this->once())
				 ->method('getRememberPasswordYesValue')
				 ->will($this->returnValue($remember_pass_value));
		
		$cookie_processor = $this->getMock('\CentralApps\Authentication\Processors\CookieInterface');
		$cookie_processor->expects($this->once())
						 ->method('rememberUser')
						 ->with($this->equalTo($cookie_values));
		$settings->expects($this->any())
				 ->method('getCookieProcessor')
				 ->will($this->returnValue($cookie_processor));
				 		 		 
		$processor = new Processor($settings, $post_data);
		$processor->checkForAuthentication();
	}

	public function testLogout()
	{
		$cookie_processor = $this->getMock('\CentralApps\Authentication\Processors\CookieInterface');
		$cookie_processor->expects($this->once())
						 ->method('logout');
		$session_processor = $this->getMock('\CentralApps\Authentication\Processors\SessionInterface');
		$session_processor->expects($this->once())
						  ->method('logout');
		
		$settings = $this->getMock('\CentralApps\Authentication\SettingsProviderInterface');
		$settings->expects($this->once())
				 ->method('getCookieProcessor')
				 ->will($this->returnValue($cookie_processor));
		$settings->expects($this->once())
				 ->method('getSessionProcessor')
				 ->will($this->returnValue($session_processor));
		
		$processor = new \CentralApps\Authentication\Processor($settings);
		$processor->logout();
	}
	
}
