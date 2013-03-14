<?php
namespace CentralApps\Authentication\Providers;

class Container extends \SplPriorityQueue
{
	public function __construct()
	{
		$this->setExtractFlags(\SplPriorityQueue::EXTR_DATA);
	}
	
	public function insert($value, $priority)
	{
		if(! $value instanceof ProviderInterface) {
			throw new \LogicException("Only objects which implement ProviderInterface can be added to the provider container");
		}
		parent::insert($value, $priority);
	}
}
