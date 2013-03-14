<?php
namespace CentralApps\Authentication\Providers;

class SessionProvider implements SessionPersistantProviderInterface
{
	protected $request;
	protected $userFactory;
	protected $userGateway;
	
	protected $sessionName = 'CA_AUTH_USER_ID';
	
	public function __construct(array $request, \CentralApps\Authentication\UserFactoryInterface $user_factory, \CentralApps\Authentication\UserGateway $user_gateway)
	{
		$this->request = $request;
		$this->userFactory = $user_factory;
		$this->userGateway = $user_gateway;
	}
	
	public function setSessionName($session_name)
	{
		$this->sessionName = $session_name;
	}
	
	public function hasAttemptedToLoginWithProvider()
	{
		return (isset($_SESSION[$this->sessionName]));
	}
	
	public function processLoginAttempt()
	{
		try {
			$user_id = (isset($_SESSION[$this->sessionName])) ? intval($_SESSION[$this->sessionName]) : 0;
 			 return $this->userFactory->getUserByUserId($user_id);
		} catch (\Exception $e) {
			return null;
		}
	}
	
	public function persistLogin()
	{
		$_SESSION[$this->sessionName] = $this->userGateway->getUserId();
 	}
	
	public function logout()
	{
		unset($_SESSION[$this->sessionName]);
	}
	
	public function userWantsToBeRemembered()
	{
		return false;
	}
}
