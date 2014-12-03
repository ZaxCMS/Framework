<?php

namespace Zax\Model\Doctrine;

use Nette\DI\Container;
use Nette\Object;

class LazyEntityManagerAccessor extends Object implements IEntityManagerAccessor {

	protected $container;

	public function __construct(Container $container) {
		$this->container = $container;
	}

	public function getEntityManager() {
		return $this->container->getByType('Kdyby\Doctrine\EntityManager');
	}

}
