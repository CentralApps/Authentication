<?php
namespace CentralApps\Authentication;

class Processor {

	protected $usernameField = null;
	protected $passwordField = null;
	protected $rememberPasswordField = null;
	protected $rememberPasswordYesValue;
	protected $sessionName = null;
	protected $cookieNames = array();

	protected $providers;

	protected $userFactory = null;
	protected $userGateway = null;
	protected $sessionProcessor = null;
	protected $cookieProcessor = null;

	protected $postData;
	protected $loginAttempted=false;

	public function __construct(SettingsProviderInterface $settings_provider, $providers=null, $post_data=null)
	{
		$this->providers = $providers;
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
	public function checkForAuthentication()
	{
		$this->attemptToLogin();
		if(is_object($this->userGateway) && is_object($this->userGateway->user) && $this->shouldPersist()) {
		    $this->persistLogin();
        }
	}

	public function attemptToLogin()
	{
		$this->providers->rewind();
        $providers = clone $this->providers;
		while($providers->valid()) {
			$provider = $providers->current();
			if($provider->hasAttemptedToLoginWithProvider()) {
				$this->loginAttempted = true;
				$this->userGateway->user = $provider->processLoginAttempt();
				if(!is_null($this->userGateway->user)) {
					// let's only break if the attempt was successful, this way
					// if someone has the wrong cookies but tries to use login form
					// it won't fail at the first hurdle
					break;
				}
			}
			$providers->next();
		}
	}

	public function shouldPersist()
	{
		if(! is_null($this->providers)) {
			$providers = clone $this->providers;
			while($providers->valid()) {
				$provider = $providers->current();
				if(!$provider->shouldPersist()) {
					return false;
				}
				$providers->next();
			}
		}

		return true;
	}

	public function rememberPasswordIfRequested()
	{
		if($this->userWantsToBeRemembered()) {
			$this->rememberUser();
		}
	}

	public function hasAttemptedToLogin()
	{
		return $this->loginAttempted;
	}

	public function getUser()
	{
		return $this->userGateway->user;
	}

	public function logout()
	{
		$this->providers->rewind();
        $providers = clone $this->providers;
		while($providers->valid()) {
			$provider = $providers->current();
			$provider->logout();
			$providers->next();
		}
	}

	public function persistLogin()
	{
		$this->providers->rewind();
        $providers = clone $this->providers;
		while($providers->valid()) {
            $provider = $providers->current();

			if($provider instanceof Providers\PersistantProviderInterface) {
				$provider->persistLogin();
			}
			$providers->next();
		}
	}

	public function userWantsToBeRemembered()
	{
		$this->providers->rewind();
        $providers = clone $this->providers;
		while($providers->valid()) {
			$provider = $providers->current();
			if($provider->userWantsToBeRemembered()) {
				return true;
			}
			$providers->next();
		}

		return false;
	}

	public function rememberUser()
	{
		$this->providers->rewind();
        $providers = clone $this->providers;
		while($providers->valid()) {
			$provider = $providers->current();
			if($provider instanceof Providers\CookiePersistantProviderInterface) {
				$provider->rememberUser();
			}

			$providers->next();
		}
		//$this->sessionProcessor->rememberUser();
		//$this->cookieProcessor->rememberUser($this->userGateway->getCookieValues());
	}

	public function authenticateFromUsernameAndPassword($username, $password)
	{
		try {
			$user = $this->userFactory->getUserFromUsernameAndPassword($username, $password);
		} catch(\Exception $e) {
			return null;
		}

		return $user;
	}

	public function manualLogin($username, $password)
    {
        $this->userGateway->user = $this->authenticateFromUsernameAndPassword($username, $password);
        if(!is_null($this->userGateway->user) && (!empty($this->userGateway->user))) {
            $this->persistLogin();
        }

        return $this->userGateway->user;
    }

    public function manualLoginFromId($id)
    {
    	$this->userGateway->user = $this->authenticateFromUserId($id);
    	if (!is_null($this->userGateway->user) && (!empty($this->userGateway->user))) {
    		$this->persistLogin();
    	}

    	return $this->userGateway->user;
    }

	public function authenticateFromUserId($user_id)
	{
		try {
			$user = $this->userFactory->getUserByUserId($user_id);
		} catch(\Exception $e) {
			return null;
		}

		return $user;
	}

	public function getProviders()
	{
		return $this->providers;
	}
}
