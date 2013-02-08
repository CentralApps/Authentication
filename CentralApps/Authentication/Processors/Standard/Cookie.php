<?php
namespace CentralApps\Authentication\Processors\Standard;

class Cookie implements \CentralApps\Authentication\Processors\CookieInterface 
{
	
	protected $cookieNames = null;
	
	public function __construct($cookie_names=null)
	{
		$this->cookieNames = $cookie_names;
	}
	
	public function checkForAuthenticationCookie()
	{
		foreach($this->cookieNames as $cookie_name) {
			if(!isset($_COOKIE[$cookie_name])) {
				return false;
			}
		}
		return true;
	}
	
	public function getCookieValues()
	{
		$cookie_values = array();
		foreach($this->cookieNames as $cookie_name) {
			// could probably do this with an array_ function instead
			$cookie_values[$cookie_name] = $_SESSION[$cookie_name];
		}
		return $cookie_values;
	}
	
	public function logout()
	{
		foreach($this->cookieNames as $cookie_name) {
			setcookie($cookie_name,"",time()-3600);
		}
	}
	
}
