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
			$cookie_values[$cookie_name] = $_COOKIE[$cookie_name];
		}
		return $cookie_values;
	}
	
	public function rememberUser($cookie_values)
	{
		$this->setCookieValues($cookieValues);
	}
	
	public function setCookieValues($cookie_values)
    {
        foreach($cookie_values as $key => $value) {
            setcookie($key, $value, time() + (86400 * 7), "/");
        }
    }

    public function logout()
    {
        foreach($this->cookieNames as $cookie_name) {
            setcookie($cookie_name, "", time() - 3600, "/");
        }
    }
	
}
