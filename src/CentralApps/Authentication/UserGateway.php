<?php
namespace CentralApps\Authentication;

class UserGateway {
	
	public $user=null;
	
	public function getUserId()
	{
		return $this->user->getId();
	}
	
	public function updateLastLoginTime($time)
	{
		// do nothing
	}
	
	public function getPasswordHash()
	{
		return $this->user->getPassword();
	}
	
	public function getCookieValues()
	{
		return array( 'user_id' => $this->getUserId(), 'user_hash' => sha1(sha1(md5($this->getPasswordHash()))));
	}
}
