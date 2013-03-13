<?php
namespace CentralApps\Authentication\Providers;

interface PersistantProviderInterface extends ProviderInterface
{
	public function persistLogin();
}
