<?php

namespace Zax\Model\Doctrine;
use Zax,
	Kdyby,
	Nette;

interface IQueryObjectFilter {

	/** @return Kdyby\Doctrine\QueryObject */
	public function filterQueryObject(Kdyby\Doctrine\QueryObject $queryObject);

}