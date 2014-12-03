<?php

namespace Zax\Model\Doctrine;
use Zax,
	Kdyby,
	Nette;

interface IFilterable {

	/** @return Kdyby\Doctrine\ResultSet */
	public function getFilteredResultSet();

}