<?php

namespace Zax\Model\Doctrine;

use Kdyby;

interface IEntityManagerAccessor {

	/** @return Kdyby\Doctrine\EntityManager */
	public function getEntityManager();

}
