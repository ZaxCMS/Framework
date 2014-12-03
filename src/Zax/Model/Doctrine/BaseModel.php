<?php

namespace Zax\Model\Doctrine;

use Nette\Object;
use Kdyby;

class BaseModel extends Object {

	private $entityManagerAccessor;

	private $entityManager;

	public function __construct(IEntityManagerAccessor $entityManagerAccessor) {
		$this->entityManagerAccessor = $entityManagerAccessor;
	}

	/** @return Kdyby\Doctrine\EntityManager */
	protected function getEntityManager() {
		if($this->entityManager === NULL) {
			$this->entityManager = $this->entityManagerAccessor->getEntityManager();
		}
		return $this->entityManager;
	}

}
