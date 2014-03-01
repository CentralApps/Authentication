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
    }

    public function testSetUserIdCookieName()
    {
        $reflection = new \ReflectionClass('\CentralApps\Authentication\UserGateway');
        $property = $reflection->getProperty("userIdCookieName");
        $property->setAccessible(true);
        $this->_object->setUserIdCookieName('some_cookie_name');
        $this->assertEquals('some_cookie_name', $property->getValue($this->_object));
    }

    public function testSetUserHashCookieName()
    {
        $reflection = new \ReflectionClass('\CentralApps\Authentication\UserGateway');
        $property = $reflection->getProperty("userHashCookieName");
        $property->setAccessible(true);
        $this->_object->setUserHashCookieName('some_cookie_name_hash');
        $this->assertEquals('some_cookie_name_hash', $property->getValue($this->_object));
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
        $this->assertArrayHasKey('CA_AUTH_COOKIE_USER_ID', $cookie_values);
        $this->assertArrayHasKey('CA_AUTH_COOKIE_USER_HASH', $cookie_values);
        $this->assertEquals(sha1(sha1(md5('some-hash'))), $cookie_values['CA_AUTH_COOKIE_USER_HASH']);
        $this->assertEquals(1, $cookie_values['CA_AUTH_COOKIE_USER_ID']);
    }
}
