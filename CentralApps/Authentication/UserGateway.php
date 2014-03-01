<?php
namespace CentralApps\Authentication;

class UserGateway
{
    public $user = null;
    protected $userIdCookieName = 'CA_AUTH_COOKIE_USER_ID';
    protected $userHashCookieName = 'CA_AUTH_COOKIE_USER_HASH';

    public function setUserIdCookieName($name)
    {
        $this->userIdCookieName = $name;
    }

    public function setUserHashCookieName($name)
    {
        $this->userHashCookieName = $name;
    }

    public function getUserId()
    {
        return $this->user->getId();
    }

    public function getPasswordHash()
    {
        return $this->user->getPassword();
    }

    public function getCookieValues()
    {
        return array($this->userIdCookieName => $this->getUserId(), $this->userHashCookieName => sha1(sha1(md5($this->getPasswordHash()))));
    }
}
