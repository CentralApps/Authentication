<?php
namespace CentralApps\Authentication;

/**
 * @small
 */
class UserGatewayTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
    	$this->_object = new UserGateway();
		$this->_stub = $this->getMock('\CentralApps\Authentication\UserInterface');
        //$stub = $this->getMockForAbstractClass('\CentralApps\Core\AbstractCollection');
        //$this->_object = $stub;
    }
	
	public function testGetUserId()
	{
		$this->_stub->expects($this->once())
             ->method('getId')
             ->will($this->returnValue(1));
		$this->_object->user = $this->_stub;
		$this->assertEquals(1, $this->_object->getUserId());
	}
	
	public function testGetPasswordHash()
	{
		$this->_stub->expects($this->once())
             ->method('getPassword')
             ->will($this->returnValue('some-hash'));
		$this->_object->user = $this->_stub;
		$this->assertEquals('some-hash', $this->_object->getPasswordHash());	
	}
	
	/**
	 * @covers CentralApps\Authentication\UserGateway::getCookieValues
	 */
	public function testGetCookieValues()
	{
		$this->_stub->expects($this->once())
             ->method('getPassword')
             ->will($this->returnValue('some-hash'));
		$this->_stub->expects($this->once())
             ->method('getId')
             ->will($this->returnValue(1));
		$this->_object->user = $this->_stub;
		$cookie_values = $this->_object->getCookieValues();
		$this->assertArrayHasKey('user_id', $cookie_values);
		$this->assertArrayHasKey('user_hash', $cookie_values);
		$this->assertEquals(sha1(sha1(md5('some-hash'))), $cookie_values['user_hash']);	
		$this->assertEquals(1, $cookie_values['user_id']);
	}
	
    

}
