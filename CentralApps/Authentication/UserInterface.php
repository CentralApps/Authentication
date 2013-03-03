<?php
namespace CentralApps\Authentication;

interface UserInterface
{
	public function getId();
	public function getPassword();
}
