<?php
namespace CentralApps\Authentication;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->_processor = new Processor($this->getMock('\CentralApps\Authentication\SettingsProviderInterface'), null, array());
		$this->_container = new Providers\Container();
	}
	
	public function testConstructor()
	{
		$post_data = array('some', 'data');
		$processor = new Processor($this->getMock('\CentralApps\Authentication\SettingsProviderInterface'), null, $post_data);
		$class = new \ReflectionClass("\CentralApps\Authentication\Processor");
        $property = $class->getProperty('postData');
        $property->setAccessible(true);
        $value = $property->getValue($processor);
		$this->assertEquals($post_data, $value);
	}
	
	public function testCheckForAuthentication()
	{
		$processor = $this->getMockBuilder('\CentralApps\Authentication\Processor')
					      ->disableOriginalConstructor()
						  ->setMethods(array('attemptToLogin', 'persistLogin'))
					      ->getMock();
		$processor->expects($this->once())
				  ->method('attemptToLogin');
		$processor->expects($this->once())
				  ->method('persistLogin');
		$processor->checkForAuthentication();
	}
	
	/**
	 * @covers \CentralApps\Authentication\Processor::attemptToLogin
	 * @covers \CentralApps\Authentication\Processor::authenticateFromUserId
	 */
	public function testLoggingIn()
	{
		$user = new \stdClass;
		$user->userId = 1;
		
		$user_gateway = $this->getMock('\CentralApps\Authentication\UserGateway');
		
		$session_provider = $this->getMock('\CentralApps\Authentication\Providers\ProviderInterface');
		$session_provider->expects($this->once())
						 ->method('hasAttemptedToLoginWithProvider')
						 ->will($this->returnValue(false));
						 
		$session_provider_2 = $this->getMock('\CentralApps\Authentication\Providers\ProviderInterface');
		$session_provider_2->expects($this->once())
						 ->method('hasAttemptedToLoginWithProvider')
						 ->will($this->returnValue(true));
		$session_provider_2->expects($this->once())
						 ->method('processLoginAttempt')
						 ->will($this->returnValue($user));				 
						 
		$this->_container->insert($session_provider, 10);
		$this->_container->insert($session_provider_2, 0);
		
		$settings_provider = $this->getMock('\CentralApps\Authentication\SettingsProviderInterface');
		$settings_provider->expects($this->once())
				 ->method('getUserGateway')
				 ->will($this->returnValue($user_gateway));
				 
		$processor = new Processor($settings_provider, $this->_container);
		$processor->attemptToLogin();			 
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
	
	public function testGetUser()
	{
		$user = new \stdClass();
		$user->id = 8889;
		
		$user_gateway = $this->getMock('\CentralApps\Authentication\UserGateway');
		$user_gateway->user = $user;
		
		
		$settings = $this->getMock('\CentralApps\Authentication\SettingsProviderInterface');
		$settings->expects($this->once())
				 ->method('getUserGateway')
				 ->will($this->returnValue($user_gateway));
		
		$processor = new \CentralApps\Authentication\Processor($settings);
		$this->assertEquals($user, $processor->getUser());
	}
	
	public function testAuthenticateFromUserId()
	{
		$fake_user_id = 5;
		$user = new \stdClass();
		$user->id = 444;
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

		$processor = new \CentralApps\Authentication\Processor($settings);

		$this->assertNull($processor->authenticateFromUserId($fake_user_id));
		
		$user_factory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');
		$user_factory->expects($this->once())
					 ->method('getUserByUserId')
					 ->with($this->equalTo($fake_user_id))
					 ->will($this->returnValue($user));
		$settings = $this->getMock('\CentralApps\Authentication\SettingsProviderInterface');
		$settings->expects($this->once())
				 ->method('getUserFactory')
				 ->will($this->returnValue($user_factory));	

		$processor = new \CentralApps\Authentication\Processor($settings);

		$this->assertEquals($user, $processor->authenticateFromUserId($fake_user_id));
		
	}

	public function testAuthenticateFromUsernameAndPassword()
	{
		$fake_user = 'fake-user';
		$fake_pass = 'fake-pass';
		$user = new \stdClass();
		$user->id = 12222;
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
		$processor = new \CentralApps\Authentication\Processor($settings);

		$this->assertNull($processor->authenticateFromUsernameAndPassword($fake_user, $fake_pass));
		
		$user_factory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');
		$user_factory->expects($this->once())
					 ->method('getUserFromUsernameAndPassword')
					 ->with($this->equalTo($fake_user), $this->equalTo($fake_pass))
					 ->will($this->returnValue($user));
		$settings = $this->getMock('\CentralApps\Authentication\SettingsProviderInterface');
		$settings->expects($this->once())
				 ->method('getUserFactory')
				 ->will($this->returnValue($user_factory));	
				 
		$processor = new \CentralApps\Authentication\Processor($settings);

		$this->assertEquals($user, $processor->authenticateFromUsernameAndPassword($fake_user, $fake_pass));
		
	}

	public function testUserWantsToBeRemembered()
	{
		$session_provider = $this->getMock('\CentralApps\Authentication\Providers\ProviderInterface');
		$session_provider->expects($this->once())
						 ->method('userWantsToBeRemembered')
						 ->will($this->returnValue(false));
						 
		$session_provider_2 = $this->getMock('\CentralApps\Authentication\Providers\ProviderInterface');
		$session_provider_2->expects($this->once())
						 ->method('userWantsToBeRemembered')
						 ->will($this->returnValue(true));
						 				 
		$this->_container->insert($session_provider,10);
		$this->_container->insert($session_provider_2,0);
		
		$processor = new \CentralApps\Authentication\Processor($this->getMock('\CentralApps\Authentication\SettingsProviderInterface'), $this->_container);
		$this->assertTrue($processor->userWantsToBeRemembered());
		
		
		$session_provider = $this->getMock('\CentralApps\Authentication\Providers\ProviderInterface');
		$session_provider->expects($this->once())
						 ->method('userWantsToBeRemembered')
						 ->will($this->returnValue(false));
						 
		$session_provider_2 = $this->getMock('\CentralApps\Authentication\Providers\ProviderInterface');
		$session_provider_2->expects($this->once())
						 ->method('userWantsToBeRemembered')
						 ->will($this->returnValue(false));
						 
		$this->_container = new Providers\Container();				 				 
		$this->_container->insert($session_provider,10);
		$this->_container->insert($session_provider_2,0);
		
		$processor = new \CentralApps\Authentication\Processor($this->getMock('\CentralApps\Authentication\SettingsProviderInterface'), $this->_container);
		$this->assertFalse($processor->userWantsToBeRemembered());
	}

	public function testLogout()
	{
		$session_provider = $this->getMock('\CentralApps\Authentication\Providers\ProviderInterface');
		$session_provider->expects($this->once())
						 ->method('logout');
						 
		$session_provider_2 = $this->getMock('\CentralApps\Authentication\Providers\ProviderInterface');
		$session_provider_2->expects($this->once())
						 ->method('logout');
						 				 
		$this->_container->insert($session_provider,0);
		$this->_container->insert($session_provider_2,0);
		
		$processor = new \CentralApps\Authentication\Processor($this->getMock('\CentralApps\Authentication\SettingsProviderInterface'), $this->_container);
		$processor->logout();
	}
	
	public function testPersistLogins()
	{
		$session_provider = $this->getMock('\CentralApps\Authentication\Providers\PersistantProviderInterface');
		$session_provider->expects($this->once())
						 ->method('persistLogin');
						 
		$session_provider_2 = $this->getMock('\CentralApps\Authentication\Providers\ProviderInterface');
		$session_provider_2->expects($this->never())
						 ->method('persistLogin');
						 				 
		$this->_container->insert($session_provider,0);
		$this->_container->insert($session_provider_2,0);
		
		$processor = new \CentralApps\Authentication\Processor($this->getMock('\CentralApps\Authentication\SettingsProviderInterface'), $this->_container);
		$processor->persistLogin();
	}
	
	public function testRememberUser()
	{
		$session_provider = $this->getMock('\CentralApps\Authentication\Providers\PersistantProviderInterface');
		$session_provider->expects($this->never())
						 ->method('rememberUser');
						 
		$session_provider_2 = $this->getMock('\CentralApps\Authentication\Providers\ProviderInterface');
		$session_provider_2->expects($this->never())
						 ->method('rememberUser');
						 
		$session_provider_3 = $this->getMock('\CentralApps\Authentication\Providers\CookiePersistantProviderInterface');
		$session_provider_3->expects($this->once())
						 ->method('rememberUser');
						 				 
		$this->_container->insert($session_provider,0);
		$this->_container->insert($session_provider_2,0);
		$this->_container->insert($session_provider_3,0);
		
		$processor = new \CentralApps\Authentication\Processor($this->getMock('\CentralApps\Authentication\SettingsProviderInterface'), $this->_container);
		$processor->rememberUser();
	}
	
	public function testRememberPasswordIfRequested()
	{
		$processor = $this->getMockBuilder('\CentralApps\Authentication\Processor')
					      ->disableOriginalConstructor()
						  ->setMethods(array('userWantsToBeRemembered', 'rememberUser'))
					      ->getMock();
		$processor->expects($this->once())
				  ->method('userWantsToBeRemembered')
				  ->will($this->returnValue(false));
		$processor->expects($this->never())
				  ->method('rememberUser');
		$processor->rememberPasswordIfRequested();
		
		$processor = $this->getMockBuilder('\CentralApps\Authentication\Processor')
					      ->disableOriginalConstructor()
						  ->setMethods(array('userWantsToBeRemembered', 'rememberUser'))
					      ->getMock();
		$processor->expects($this->once())
				  ->method('userWantsToBeRemembered')
				  ->will($this->returnValue(true));
		$processor->expects($this->once())
				  ->method('rememberUser');
		$processor->rememberPasswordIfRequested();
				  
	}
	
	public function testManualLogin()
	{
		$username = 'test-user';
		$password = 'test-pass';
		$processor = $this->getMockBuilder('\CentralApps\Authentication\Processor')
					      ->disableOriginalConstructor()
						  ->setMethods(array('authenticateFromUsernameAndPassword', 'persistLogin'))
					      ->getMock();
						  
		
		$processor->expects($this->once())
				  ->method('authenticateFromUsernameAndPassword')
				  ->with($this->equalTo($username), $this->equalTo($password))
				  ->will($this->returnValue(null));
		$processor->expects($this->never())
				  ->method('persistLogin');
		
		$user_gateway = new \stdClass();
		$user_gateway->user = null;
		
		$reflection = new \ReflectionClass('\CentralApps\Authentication\Processor');
		$property = $reflection->getProperty("userGateway");
		$property->setAccessible(true);
		$property->setValue($processor, $user_gateway);
		
		$this->assertNull($processor->manualLogin($username, $password));
						  
		$user_gateway = new \stdClass();
		$user_gateway->user = new \stdClass();
		$user_gateway->user->id = 333;
		
		$processor = $this->getMockBuilder('\CentralApps\Authentication\Processor')
					      ->disableOriginalConstructor()
						  ->setMethods(array('authenticateFromUsernameAndPassword', 'persistLogin'))
					      ->getMock();
		$property->setValue($processor, $user_gateway);
		
		$processor->expects($this->once())
				  ->method('authenticateFromUsernameAndPassword')
				  ->with($this->equalTo($username), $this->equalTo($password))
				  ->will($this->returnValue($user_gateway->user));
		$processor->expects($this->once())
				  ->method('persistLogin');
		
		$this->assertEquals($user_gateway->user, $processor->manualLogin($username, $password));
	}
	
	
	
}
