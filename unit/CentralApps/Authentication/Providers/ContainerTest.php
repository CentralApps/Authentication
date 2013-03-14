<?php
namespace CentralApps\Authentication\Providers;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
	public function testContainerDoesntAcceptNonProviderInterfaces()
	{
		$container = new Container();
		$this->setExpectedException('\LogicException');
		$container->insert(new \stdClass(), 0);
	}
}
