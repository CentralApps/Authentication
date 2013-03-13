<?php
namespace CentralApps\Authentication\Providers;

class CookieProvider implements CookiePersistantProviderInterface
{
	protected $request;
	protected $cookies = array();
	protected $userFactory;
	protected $userGateway;
	
	protected $cookieNames = array('CA_AUTH_COOKIE_USER_ID', 'CA_AUTH_COOKIE_USER_HASH');
	
	public function __construct(array $request, \CentralApps\Authentication\UserFactoryInterface $user_factory, \CentralApps\Authentication\UserGatewayInterface $user_gateway)
	{
		$this->request = $request;
		$this->userFactory = $user_factory;
		$this->userGateway = $user_gateway;
		if(isset($request->cookies) && is_array($request->cookies)) {
			$this->cookies = $request->cookies;
		}
	}
	
	public function setCookieNames($cookie_names)
	{
		$this->cookieNames = $cookie_names;
	}
	
	public function hasAttemptedToLoginWithProvider()
	{
		foreach($this->cookieNames as $cookie_name) {
			if(!isset($this->cookies[$cookie_name])) {
				return false;
			}
		}
		return true;
	}
	
	public function processLoginAttempt()
	{
		$username = (isset($this->post[$this->usernameField])) ? $this->post[$this->usernameField] : '';
		$password = (isset($this->post[$this->passwordField])) ? $this->post[$this->passwordField] : '';
 		try {
			 return $this->userFactory->getUserFromUsernameAndPassword($username, $password);
		} catch (\Exception $e) {
			return null;
		}
	}
	
	public function logout()
	{
		foreach($this->cookieNames as $cookie_name) {
			setcookie($cookie_name, "", time() - 3600, "/");
		}
		return true;
	}
	
	public function rememberUser($ttl=604800)
	{
		$cookie_values = $this->userGateway->getCookieValues();
		foreach($cookie_values as $cookie_name => $cookie_value) {
			setcookie($cookie_name, $cookie_value, time() + (86400 * 7), "/");
		}
 	}
	
	public function userWantsToBeRemembered()
	{
		return false;
	}
}
