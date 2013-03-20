<?php
namespace CentralApps\Authentication\Providers;

class UsernamePasswordProviderTest extends \PHPUnit_Framework_TestCase
{
	
	public function setUp()
	{
		$request = array('test', 'request', 'post' => array('some' => 'post', 'data' => 'data'));
		$this->_userFactory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');
		$this->_userGateway = $this->getMock('\CentralApps\Authentication\UserGateway');
		$this->_provider = new UsernamePasswordProvider($request, $this->_userFactory, $this->_userGateway);
		$reflection = new \ReflectionClass("\CentralApps\Authentication\Providers\UsernamePasswordProvider");
		$submission_field = $reflection->getProperty("submissionField");
		$submission_field->setAccessible(true);
		$this->submissionField = $submission_field->getValue($this->_provider);
		
		$username_field = $reflection->getProperty("usernameField");
		$username_field->setAccessible(true);
		$this->usernameField = $username_field->getValue($this->_provider);
		
		$password_field = $reflection->getProperty("passwordField");
		$password_field->setAccessible(true);
		$this->passwordField = $password_field->getValue($this->_provider);
		
		$remember_field = $reflection->getProperty("rememberField");
		$remember_field->setAccessible(true);
		$this->rememberField = $remember_field->getValue($this->_provider);
		
		$remember_yes = $reflection->getProperty("rememberFieldYesValue");
		$remember_yes->setAccessible(true);
		$this->rememberYes = $remember_yes->getValue($this->_provider);
		
	}
	
	public function testConstructor()
	{
		$request = array('test', 'request', 'post' => array('some' => 'post', 'data' => 'data'));
		$user_factory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');
		$user_gateway = $this->getMock('\CentralApps\Authentication\UserGateway');
		$provider = new UsernamePasswordProvider($request, $user_factory, $user_gateway);
		$reflection = new \ReflectionClass("\CentralApps\Authentication\Providers\UsernamePasswordProvider");
		$request_property = $reflection->getProperty("request");
		$request_property->setAccessible(true);
		$factory_property = $reflection->getProperty("userFactory");
		$factory_property->setAccessible(true);
		$gateway_property = $reflection->getProperty("userGateway");
		$gateway_property->setAccessible(true);
		$post_property = $reflection->getProperty("post");
		$post_property->setAccessible(true);
		$this->assertEquals($request, $request_property->getValue($provider));
		$this->assertEquals($user_factory, $factory_property->getValue($provider));
		$this->assertEquals($user_gateway, $gateway_property->getValue($provider));
		$this->assertEquals($request['post'], $post_property->getValue($provider));
	}
	
	public function testHasAttemptedToLoginWithProvider()
	{
		$request = array( 'post' => array() );
		$provider = new UsernamePasswordProvider($request, $this->_userFactory, $this->_userGateway);
		$this->assertFalse($provider->hasAttemptedToLoginWithProvider());
		$request = array( 'post' => array( $this->submissionField => 'something') );
		$provider = new UsernamePasswordProvider($request, $this->_userFactory, $this->_userGateway);
		$this->assertTrue($provider->hasAttemptedToLoginWithProvider());
	}
	
	public function testProcessLoginAttempt()
	{
		$username = 'a-username';
		$password = 'a-password';
		$user_id = 345;
		
		$user_factory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');
		$user_factory->expects($this->once())
					 ->method('getUserFromUsernameAndPassword')
					 ->with($this->equalTo(''), $this->equalTo(''))
					 ->will($this->throwException(new \Exception()));
		$provider = new UsernamePasswordProvider(array(), $user_factory, $this->_userGateway);			 
		$this->assertNull($provider->processLoginAttempt());

		
		$user_factory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');
		$user = new \stdClass();
		$user->id = $user_id;
		$user_factory->expects($this->once())
					 ->method('getUserFromUsernameAndPassword')
					 ->with($this->equalTo($username), $this->equalTo($password))
					 ->will($this->returnValue($user));
		$provider = new UsernamePasswordProvider(array('post' => array($this->submissionField => 'something', $this->usernameField => $username, $this->passwordField => $password) ), $user_factory, $this->_userGateway);			 
		$this->assertTrue($provider->hasAttemptedToLoginWithProvider());
		$this->assertEquals($user, $provider->processLoginAttempt());
	}
	
	public function testLogout()
	{
		$this->assertTrue($this->_provider->logout());
	}
	
	public function testUserWantsToBeRemembered()
	{
		$this->assertFalse($this->_provider->userWantsToBeRemembered());
		$provider = new UsernamePasswordProvider(array( 'post' => array($this->rememberField => $this->rememberYes)), $this->_userFactory, $this->_userGateway);			 
		$this->assertTrue($provider->userWantsToBeRemembered());
	}
	
	public function testSetUsernameField()
	{
		$value = 'new_value';
		$this->_provider->setUsernameField($value);
		$reflection = new \ReflectionClass("\CentralApps\Authentication\Providers\UsernamePasswordProvider");
		$request_property = $reflection->getProperty("usernameField");
		$request_property->setAccessible(true);
		$this->assertEquals($value, $request_property->getValue($this->_provider));
	}
	
	public function testSetPasswordField()
	{
		$value = 'new_value';
		$this->_provider->setPasswordField($value);
		$reflection = new \ReflectionClass("\CentralApps\Authentication\Providers\UsernamePasswordProvider");
		$request_property = $reflection->getProperty("passwordField");
		$request_property->setAccessible(true);
		$this->assertEquals($value, $request_property->getValue($this->_provider));
	}
	
	public function testSetSubmissionField()
	{
		$value = 'new_value';
		$this->_provider->setSubmissionField($value);
		$reflection = new \ReflectionClass("\CentralApps\Authentication\Providers\UsernamePasswordProvider");
		$request_property = $reflection->getProperty("submissionField");
		$request_property->setAccessible(true);
		$this->assertEquals($value, $request_property->getValue($this->_provider));
	}
	
	public function testSetRememberField()
	{
		$value = 'new_value';
		$this->_provider->setRememberField($value);
		$reflection = new \ReflectionClass("\CentralApps\Authentication\Providers\UsernamePasswordProvider");
		$request_property = $reflection->getProperty("rememberField");
		$request_property->setAccessible(true);
		$this->assertEquals($value, $request_property->getValue($this->_provider));
	}
	
	public function testSetRememberFieldYesValue()
	{
		$value = 'new_value';
		$this->_provider->setRememberFieldYesValue($value);
		$reflection = new \ReflectionClass("\CentralApps\Authentication\Providers\UsernamePasswordProvider");
		$request_property = $reflection->getProperty("rememberFieldYesValue");
		$request_property->setAccessible(true);
		$this->assertEquals($value, $request_property->getValue($this->_provider));
	}
	
	public function testShouldPersist()
	 {
	 	$this->assertTrue($this->_provider->shouldPersist());
	 }
}
