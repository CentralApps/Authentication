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
	
	public function testCheckForAuthentication()
	{
		$this->_processor->checkForAuthentication(true);
		//$this->assertTrue(true);
	}
}
