<?php

namespace Zax\Model\Doctrine;
use Zax,
	Kdyby,
	Nette;

interface IResultSetFilter {

	/** @return Kdyby\Doctrine\ResultSet */
	public function filterResultSet(Kdyby\Doctrine\ResultSet $resultSet);

}