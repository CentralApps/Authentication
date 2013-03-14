<?php
namespace CentralApps\Authentication\Providers;

interface ProviderInterface
{
	public function __construct(array $request, \CentralApps\Authentication\UserFactoryInterface $user_factory, \CentralApps\Authentication\UserGateway $user_gateway);
	public function hasAttemptedToLoginWithProvider();
	public function processLoginAttempt();
	public function logout();
	public function userWantsToBeRemembered();
}
