<?php
namespace CentralApps\Authentication;

interface SettingsProviderInterface{
	
	public function getUsernameField();
	public function getPasswordField();
	public function getRememberPasswordField();
	public function getRememberPasswordYesValue();
	public function getSessionName();
	public function getCookieNames();
	public function getUserFactory();
	public function getUserGateway();
	public function getSessionProcessor();
	public function getCookieProcessor();
}
