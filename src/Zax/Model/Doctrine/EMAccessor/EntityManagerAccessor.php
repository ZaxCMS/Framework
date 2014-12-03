<?php

namespace Zax\Model\Doctrine;

use Kdyby\Doctrine\EntityManager;
use Nette\Object;

class EntityManagerAccessor extends Object implements IEntityManagerAccessor {

	protected $entityManager;

	public function __construct(EntityManager $entityManager) {
		$this->entityManager = $entityManager;
	}

	public function getEntityManager() {
		return $this->entityManager;
	}

}
