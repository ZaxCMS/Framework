<?php

namespace Zax\Application\Routers\NiceUrl;
use Zax,
	Zax\Application\Routers\Route,
	Zax\Application\Routers\RouteList,
	Nette;
/* TODO */
class NiceUrl extends NiceUrlPart {

	protected $aliases = [];

	public function getFullName() {
		return $this->name;
	}

	public function setAliases(array $aliases) {
		$this->aliases = $aliases;
		return $this;
	}

	public function getDefinition() {
		$meta = [];
		foreach($this->getComponents(TRUE, 'Zax\Application\Routers\NiceUrl\NiceUrlPart') as $component) {
			$meta[$component->getFullName()] = $component->getDefinition();
		}
		$meta[NULL] = Helpers::createAliases($this->aliases);
		return $meta;
	}

}
