<?php
namespace CentralApps\Authentication\Processors;

interface CookieInterface
{
	public function checkForAuthenticationCookie();
	
	public function getCookieValues();
	
	public function logout();
}
