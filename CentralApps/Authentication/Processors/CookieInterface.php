<?php
namespace CentralApps\Authentication\Processors;

interface CookieInterface
{
	public function checkForAuthenticationCookie();
	
	public function getCookieValues();
	
	public function setCookieValues($cookie_values);
	
	public function logout();
}
