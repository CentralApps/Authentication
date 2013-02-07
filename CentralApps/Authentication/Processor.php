<?php
namespace CentralApps\Authentication;

class Processor {
	
	protected $usernameField = null;
	protected $passwordField = null;
	protected $rememberPasswordField = null;
	protected $rememberPasswordYesValue;
	protected $sessionName = null;
	protected $cookieNames = array();
	
	protected $userFactory = null;
	protected $userGateway = null;
	protected $sessionProcessor = null;
	protected $cookieProcessor = null;
	
	protected $postData;
	
	public function __construct(SettingsProviderInterface $settings_provider, $post_data=null)
	{
		$this->postData = (!is_null($post_data)) ? $post_data : $_POST;
		$this->usernameField = $settings_provider->getUsernameField();
		$this->passwordField = $settings_provider->getPasswordField();
		$this->rememberPasswordField = $settings_provider->getRememberPasswordField();
		$this->rememberPasswordYesValue = $settings_provider->getRememberPasswordYesValue();
		$this->sessionName = $settings_provider->getSessionName();
		$this->cookieNames = $settings_provider->getCookieNames();
		$this->userFactory = $settings_provider->getUserFactory();
		$this->userGateway = $settings_provider->getUserGateway();
		$this->sessionProcessor = $settings_provider->getSessionProcessor();
		$this->cookieProcessor = $settings_provider->getCookieProcessor();
	}
	
	// use something such as $_SERVER['REQUEST_METHOD'] == 'POST' for the $login_attempt variable
	public function checkForAuthentication($login_attempt=true)
	{
		if($login_attempt && (isset($this->postData[ $this->usernameField]) || isset($this->postData[$this->passwordField]))) {
			$this->userGateway->user = $this->authenticateFromUsernameAndPassword($_POST[$this->usernameField], $this->postData[$this->passwordField]);
			if(!is_null($this->userGateway->user) && isset($this->postData[$this->rememberPasswordField]) && $this->postData[$this->rememberPasswordField] == $this->rememberPasswordYesValue) {
				$this->rememberUser();
			}
		} elseif(is_object($this->cookieProcessor) && $this->cookieProcessor->checkForAuthenticationCookie()) {
			$this->userGateway->user = $this->authenticateFromCookies($this->cookieProcessor()->getCookieValues());
		} elseif(is_object($this->sessionProcessor) && $this->sessionProcessor->checkForAuthenticationSession()) {
			$this->userGateway->user = $this->authenticateFromUserId($this->sessionProcessor()->getUserId());
		}
	}
	
	public function getUser()
	{
		return $this->userGateway->user;
	}
	
	public function logout()
	{
		$this->sessionProcessor->logout();
		$this->cookieProcessor->logout();
	}
	
	public function rememberUser()
	{
		//$this->sessionProcessor->rememberUser();
		$this->cookieProcessor->rememberUser($this->userGateway->getCookieValues());
	}
	
	private function authenticateFromUsernameAndPassword($username, $password)
	{
		try {
			$user = $this->userFactory->getUserFromUsernameAndPassword($username, $password);
		} catch(\Exception $e) {
			return null;
		}
		return $user;
	}
	
	private function authenticateFromUserId($user_id)
	{
		try {
			$user = $this->userFactory->getUserByUserId($user_id);
		} catch(\Exception $e) {
			return null;
		}
		return $user;
	}
	
	private function authenticateFromCookieValues($cookie_values)
	{
		try {
			$user = $this->userFactory->getByCookieValues($cookie_values);
		} catch(\Exception $e) {
			return null;
		}
		return $user;
	}
	
}
