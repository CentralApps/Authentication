<?php
namespace CentralApps\Authentication\Processors;

interface SessionInterface {
	
	public function __construct($session_name=null);
	public function checkForAuthenticationSession();
	public function getUserId();
	public function logout();
	
}
