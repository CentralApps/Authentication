<?php
namespace CentralApps\Authentication\Processors\Standard;

class Session implements \CentralApps\Authentication\Processors\SessionInterface {
	
	protected $sessionName = null;
	
	public function __construct($session_name=null)
	{
		$this->sessionName = $session_name;
	}
	
	public function checkForAuthenticationSession()
	{
		return isset($_SESSION[$this->sessionName]);
	}
	
	public function getUserId()
	{
		return $_SESSION[$this->sessionName];
	}
	
	public function logout()
	{
		unset($_SESSION[$this->sessionName]);
	}
	
}
