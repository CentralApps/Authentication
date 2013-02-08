<?php
namespace CentralApps\Authentication;

interface UserFactoryInterface
{
    public function getUserFromUsernameAndPassword($username, $password);
    public function getUserByUserId($user_id);
    public function getByCookieValues($cookie_values);
}
