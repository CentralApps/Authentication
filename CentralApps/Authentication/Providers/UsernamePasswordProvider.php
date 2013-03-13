<?php
namespace CentralApps\Authentication\Providers;

class SessionProvider implements ProcessorInterface
{
	protected $request;
	protected $post = array();
	protected $userFactory;
	protected $userGateway;
	
	protected $usernameField = 'username';
	protected $passwordField = 'password';
	protected $submissionField = 'authentication_attempt';
	protected $rememberField = 'remember';
	protected $rememberFieldYesValue = '1';
	
	public function __construct(array $request, \CentralApps\Authentication\UserFactoryInterface $user_factory, \CentralApps\Authentication\UserGatewayInterface $user_gateway)
	{
		$this->request = $request;
		$this->userFactory = $user_factory;
		$this->userGateway = $user_gateway;
		if(isset($request->post) && is_array($request->post)) {
			$this->post = $request->post;
		}
	}
	
	public function hasAttemptedToLoginWithProvider()
	{
		return isset($this->post[$this->submissionField]);
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
		return true;
	}
	
	public function userWantsToBeRemembered()
	{
		return (isset($this->post[$this->rememberField]) && ($this->post[$this->rememberField] == $this->rememberFieldYesValue));
	}
}
