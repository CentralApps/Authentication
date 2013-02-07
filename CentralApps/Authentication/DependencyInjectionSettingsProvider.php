<?php
namespace CentralApps\Authentication;

class DependencyInjectionSettingsProvider implements SettingsProviderInterface {
		
	private $dependency_injection_container = null;
	
	public function __construct($dependency_injection_container)
	{
		$this->dependency_injection_container = $dependency_injection_container;	
	}
	
	public function getUsernameField()
	{
		return $this->dependency_injection_container['username_field'];
	}
	
	public function getPasswordField()
	{
		return $this->dependency_injection_container['password_field'];
	}
	
	public function getRememberPasswordField()
	{
		return $this->dependency_injection_container['remember_password_field'];
	}
	
	public function getRememberPasswordYesValue()
	{
		return $this->dependency_injection_container['remember_password_yes_value'];
	}
	
	public function getSessionName()
	{
		return $this->dependency_injection_container['session_name'];
	}
	
	public function getCookieNames()
	{
		return $this->dependency_injection_container['cookie_names'];
	}
	
	public function getUserFactory()
	{
		return $this->dependency_injection_container['user_factory'];
	}
	
	public function getUserGateway()
	{
		return $this->dependency_injection_container['user_gateway'];
	}
	
	public function getSessionProcessor()
	{
		return $this->dependency_injection_container['session_processor'];
	}
	
	public function getCookieProcessor()
	{
		return $this->dependency_injection_container['cookie_processor'];
	}
	
	
}