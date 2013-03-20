<?php
namespace CentralApps\Authentication\Providers;

class CookieProviderTest extends \PHPUnit_Framework_TestCase
{
	
	protected $cookieNames = array('CA_AUTH_COOKIE_USER_ID', 'CA_AUTH_COOKIE_USER_HASH');
	
	public function setUp()
	{
		$request = array();
		$this->_userGateway = $this->getMock('\CentralApps\Authentication\UserGateway');
		$this->_userFactory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');
		$this->object = $this->getMockBuilder('\CentralApps\Authentication\Providers\CookieProvider')
					      ->disableOriginalConstructor()
						  ->setMethods(array('userWantsToBeRemembered'))
					      ->getMock();
		$this->object->__construct($request, $this->_userFactory, $this->_userGateway);
		$this->object->persistLogin();
		$this->object = $this->getMockBuilder('\CentralApps\Authentication\Providers\CookieProvider')
					      ->disableOriginalConstructor()
						  ->setMethods(array('persistLogin'))
					      ->getMock();
		$this->object->__construct($request, $this->_userFactory, $this->_userGateway);
		//$this->object = new CookieProvider(array(), $this->_userFactory, $this->_userGateway);
	}
	
	public function tearDown()
	{
		foreach($this->cookieNames as $cookie_name) {
            setcookie($cookie_name,"",time()-3600, "/");
        }
		$_COOKIE = array();
	}
	
	/**
	 * @runInSeparateProcess
	 */
	public function testSetCookieValues()
	{
		$cookie_names = array('some', 'cookie', 'names');
		$class = new \ReflectionClass("\CentralApps\Authentication\Providers\CookieProvider");
        $property = $class->getProperty('cookieNames');
        $property->setAccessible(true);
		$this->object->setCookieNames($cookie_names);
        $this->assertEquals($cookie_names, $property->getValue($this->object), "Cookie names were not set by cookie processor");
	}
	
	/**
	 * @runInSeparateProcess
	 */
	public function testHasAttemptedToLoginWithProvider()
	{
		$this->assertFalse($this->object->hasAttemptedToLoginWithProvider());
		$request = array( 'cookies' => array() );
		foreach($this->cookieNames as $cookie_name) {
            $request['cookies'][$cookie_name] = 'some fake value';
        }
		$this->object = $this->getMockBuilder('\CentralApps\Authentication\Providers\CookieProvider')
					      ->disableOriginalConstructor()
						  ->setMethods(array('userWantsToBeRemembered', 'rememberUser'))
					      ->getMock();
		$this->object->__construct($request, $this->_userFactory, $this->_userGateway);
		$this->assertTrue($this->object->hasAttemptedToLoginWithProvider());
	}
	
	/**
	 * @runInSeparateProcess
	 */
	public function testUserWantsToBeRemembered()
	{
		$this->assertFalse($this->object->userWantsToBeRemembered());
	}
	
	/**
	 * @runInSeparateProcess
	 */
	public function testProcessLoginAttempt()
	{
		$cookie_values = array();
		foreach($this->cookieNames as $cookie_name) {
			$cookie_values[$cookie_name] = 'some-value';
		}
		$cookies = array_merge($cookie_values, array('red' => 'herring'));
		$request = array('cookies' => $cookies);
		$user_factory = $this->getMock('\CentralApps\Authentication\UserFactoryInterface');
		$user_factory->expects($this->once())
					 ->method('getByCookieValues')
					 ->with($this->equalTo($cookie_values))
					 ->will($this->throwException(new \Exception()));
		$this->object->__construct($request, $user_factory, $this->_userGateway);
		$this->assertNull($this->object->processLoginAttempt());
	}

	/**
	 * @runInSeparateProcess
	 */
	public function _testRememberUser()
	{
		$cookies = array();
		foreach($this->cookieNames as $cookie_name) {
			$cookies[$cookie_name] = 'some_cookie_value_' . $cookie_name;
		}
		$request = array();
		$user_gateway = $this->getMock('\CentralApps\Authentication\UserGateway');
		$user_gateway->expects($this->once())
					 ->method('getCookieValues')
					 ->will($this->returnValue($cookies));

		$object = new CookieProvider($request, $this->_userFactory, $user_gateway);
		$object->rememberUser();
	}
	
	/**
	 * @runInSeparateProcess
	 */
	 public function _testLogout()
	 {
	 	$object = new CookieProvider(array(), $this->_userFactory, $this->_userGateway);
		$object->logout();
	 }
	 
	 public function testShouldPersist()
	 {
	 	$this->assertTrue($this->object->shouldPersist());
	 }
	

	

}
