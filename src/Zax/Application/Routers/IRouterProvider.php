<?php

namespace Zax\Application\Routers;
use Zax,
	Nette;

interface IRouterProvider {

	/** @return Nette\Application\IRouter */
	public function getRouter();

}
